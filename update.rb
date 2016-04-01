#!/usr/bin/env ruby -w -I.

this_dir = File.expand_path(File.dirname(__FILE__))
$LOAD_PATH.unshift(this_dir) unless $LOAD_PATH.include?(this_dir)

require 'fileutils'
require 'clparser'

# PHP file checker
class PHPScriptChecker
  def initialize(files)
    @files = files
  end

  def results
    lines = ''
    @files.each { |fn| lines << `php -l #{fn}` }
    result = (lines !~ /Errors/)

    [result, lines]
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
  CSS_FILES   = /\.(sa|sc|c)ss(\.map)?$/i
  IMAGE_FILES = /\.(png|jpe?g|gif)$/i
  JS_FILES    = /\.js(\.map)?/i

  FILE_TYPES = {
    php:    PHP_FILES,
    web:    WEB_FILES,
    css:    CSS_FILES,
    image:  IMAGE_FILES,
    js:     JS_FILES
  }.freeze

  attr_reader :fullpath, :fulldest

  def initialize(options)
    @options  = options
    @fullpath = File.expand_path options[:dir]
    @fulldest = File.expand_path options[:dest]

    collect_files
  end

  #--------------------------------------------------------------------------
  # Check the syntax of all the PHP sources files and return the status and
  # actual lines from the PHP interpreter as an array.
  #--------------------------------------------------------------------------

  def check_php_files
    PHPScriptChecker.new(php_files).results
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

  private

  def collect_files
    @files = top_level_dir.grep WEB_FILES
    @files += find_files(:cssdir, CSS_FILES)
    @files += find_files(:jsdir, JS_FILES)
    @files += find_files(:idir, IMAGE_FILES)
  end

  #--------------------------------------------------------------------------
  # Find the selected files in the root and also in the specified directory
  #--------------------------------------------------------------------------

  def find_files(key, pattern)
    found   = top_level_dir.grep pattern

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
  # Return a selection of files parameterised by the passed string.
  #--------------------------------------------------------------------------

  FILE_TYPES.each do |type, regex|
    define_method("#{type}_files") do
      return_files regex
    end
  end

  def return_files(re)
    @files.grep(re).map { |fn| File.join(fullpath, fn) }
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

  def top_level_dir
    Dir.new(fullpath)
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

  def initialize(options, update_filename = '.updated')
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

# Return either an updater or a conditional updater
class UpdateFactory
  def initialize(all)
    @klass = all ? SiteUpdater : CheckedSiteUpdater
  end

  def create(options)
    @klass.new(options)
  end
end

#----------------------------------------------------------------------------
# Main
#----------------------------------------------------------------------------

options = CommandLineParser.new.parse

if options[:dest].empty?
  puts 'You must specify a destination directory.' \
       ' or use the HTDOCS environment variable.'
  exit 1
end

sup = UpdateFactory.new(options[:all]).create(options)

puts "Processing #{sup.fullpath}\nChecking PHP Files..."

syntax_ok, lines = sup.check_php_files

if syntax_ok
  puts "\nOK. Copying Files to #{sup.fulldest}..."

  sup.copy_files
else
  puts lines

  puts "\nPHP Errors Detected. Stopping..."
end
