#!/usr/bin/env ruby -w -I.
require 'optparse'
require 'fileutils'

options = {
  dir:      '.',
  dest:     ENV['HTDOCS'] || '',
  cssdir:   'css',
  jsdir:    'js',
  idir:     'images',
  all:      false
}

parser = OptionParser.new do |opts|
  opts.banner = "Usage #{$PROGRAM_NAME}: [options]"

  opts.on('-s', '--dir DIR',
          "Set the source directory (Default: #{options[:dir]})") do |dir|
    options[:dir] = dir
  end

  opts.on('-o', '--dest DIR', String,
          "Set the root destination directory (Default: #{options[:dest]})") do |dir|
    options[:dest] = dir
  end

  opts.on('-c', '--cssdir DIR',
          "Set the CSS directory under the root directory (Default: #{options[:cssdir]})") do |dir|
    options[:cssdir] = dir
  end

  opts.on('-i', '--imagedir DIR',
          "Set the images directory under the root directory (Default: #{options[:idir]})") do |dir|
    options[:idir] = dir
  end

  opts.on('-j', '--jsdir DIR',
          "Set the Javascript directory under the root directory (Default: #{options[:jssdir]})") do |dir|
    options[:jsdir] = dir
  end

  opts.on('-a', '--all',
          'Copy all files, rather than updating changed files (Default: update only)') do
    options[:all] = true
  end

  opts.on_tail('-h', '--help', 'Show this help') do
    puts opts
    exit
  end
end

#----------------------------------------------------------------------------
# Collect the web type files and facilitate returning fully-qualified path
# names for each
#----------------------------------------------------------------------------
class SiteUpdater
  include FileUtils

  PHP_FILES   = /\.php$/i
  WEB_FILES   = /\.(php|html?)$/i
  CSS_FILES   = /\.(sa|sc|c)ss$/i
  IMAGE_FILES = /\.(png|jpe?g|gif)$/i
  JS_FILES    = /\.js/i

  attr_reader :fullpath, :files, :fulldest

  #--------------------------------------------------------------------------
  # Collect the source and base destination directories from the passed hash
  # and store the rest of the directories for later use.
  #--------------------------------------------------------------------------

  def initialize(options)
    @options  = options
    @fullpath = File.expand_path options[:dir]
    collect_files

    @fulldest = File.expand_path options[:dest]
  end

  #--------------------------------------------------------------------------
  # Check the syntax of all the PHP sources files and return the status and
  # actual lines from the PHP interpreter as an array.
  #--------------------------------------------------------------------------

  def check_php_files
    lines = ''
    php_files.each { |fn| lines << `php -l #{fn}` }
    result = (lines !~ /line \d/i)

    [result, lines]
  end

  #--------------------------------------------------------------------------
  # Copy all the files, possibly to separate directories
  #--------------------------------------------------------------------------

  def copy_files
    web_files.each    { |fn| copy(fn) }
    css_files.each    { |fn| copy(fn, @options[:cssdir]) }
    js_files.each     { |fn| copy(fn, @options[:jsdir]) }
    image_files.each  { |fn| copy(fn, @options[:idir]) }
  end

  #--------------------------------------------------------------------------
  # Return the lists of various types of file
  #--------------------------------------------------------------------------

  def php_files
    return_files PHP_FILES
  end

  def web_files
    return_files WEB_FILES
  end

  def css_files
    return_files CSS_FILES
  end

  def js_files
    return_files JS_FILES
  end

  def image_files
    return_files IMAGE_FILES
  end

  private

  def collect_files
    @files = Dir.new(fullpath).grep WEB_FILES
    @files += find_files(:cssdir, CSS_FILES)
    @files += find_files(:jsdir, JS_FILES)
    @files += find_files(:idir, IMAGE_FILES)
  end

  def find_files(key, pattern)
    found   = Dir.new(fullpath).grep pattern

    rel_dir = @options[key]

    return found if rel_dir.empty?

    dir = File.join(fullpath, rel_dir)

    if File.exist? dir
      Dir.new(dir).grep(pattern).each do |file|
        found << File.join(rel_dir, file)
      end
    end

    found
  end

  #--------------------------------------------------------------------------
  # Return a selection of file parameterised by the passed string.
  #--------------------------------------------------------------------------

  def return_files(re)
    files.grep(re).map { |fn| File.join(fullpath, fn) }
  end

  #--------------------------------------------------------------------------
  # Copy a file to its destination, which might be a directory off the base
  # directory. The destination directory is created if it doesn't already
  # exist.
  #--------------------------------------------------------------------------

  def copy(fqfn, dir = nil)
    fqdestdir = dir ? File.join(fulldest, dir) : fulldest

    Dir.mkdir(fqdestdir) unless File.exist? fqdestdir

    cp(fqfn, fqdestdir, verbose: true)
  end
end

#----------------------------------------------------------------------------
# Add a facility to check each file is newer than a passed update marker file
# before copying it. The status file is touched after use.
#----------------------------------------------------------------------------
class CheckedSiteUpdater < SiteUpdater
  include FileUtils

  #--------------------------------------------------------------------------
  # Initialise with the passed arguments and the update marker file to check
  # against, which will be created later if it doesn't exist.
  #--------------------------------------------------------------------------

  def initialize(options, update_filename)
    super(options)

    @update_filename  = File.expand_path update_filename
    @update_stamp     = if File.exist?(@update_filename)
                          File.mtime(@update_filename)
                        else
                          Time.new(1970, 1, 1)
                        end
  end

  #--------------------------------------------------------------------------
  # Copy the files that either don't already exist in the destination
  # directory or are newer than the update marker file. The update marker
  # file is 'touch'ed after use.
  #--------------------------------------------------------------------------

  def copy_files
    super
    touch @update_filename
  end

  private

  #--------------------------------------------------------------------------
  # Check the file against the update marker file and copy it if it doesn't
  # already exist, or is newer.
  #--------------------------------------------------------------------------

  def copy(fqfn, dir = nil)
    fqdestdir = dir ? File.join(fulldest, dir) : fulldest

    Dir.mkdir(fqdestdir) unless File.exist? fqdestdir

    cp(fqfn, fqdestdir, verbose: true) if updated?(fqfn, fqdestdir)
  end

  #--------------------------------------------------------------------------
  # Check the file against the update marker file, if it exists, otherwise
  # it's definitely an update anyway.
  #--------------------------------------------------------------------------

  def updated?(fqfn, fqdir)
    !File.exist?(File.join(fqdir, File.basename(fqfn))) ||
      (File.mtime(fqfn) > @update_stamp)
  end
end

#----------------------------------------------------------------------------
# Main
#----------------------------------------------------------------------------

begin
  parser.parse!
rescue => err
  puts 'Argument Error: ' + err.message
  exit 1
end

if options[:dest].empty?
  puts 'You must set a destination directory or set the HTDOCS variable.'
  exit 1
end

sup = if options[:all]
        SiteUpdater.new(options)
      else
        CheckedSiteUpdater.new(options, '.updated')
      end

puts "Processing #{sup.fullpath}\nChecking PHP Files..."

syntax_ok, lines = sup.check_php_files

if syntax_ok
  puts "\nOK. Copying Files to #{sup.fulldest}..."

  sup.copy_files
else
  puts lines

  puts "\nPHP Errors Detected. Stopping..."
end
