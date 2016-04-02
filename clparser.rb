require 'optparse'

# Command line parser
class CommandLineParser
  def initialize
    @options = {
      dir:      '.',
      dest:     ENV['HTDOCS'] || '',
      cssdir:   'css',
      jsdir:    'js',
      idir:     'images',
      all:      false
    }
  end

  def parse
    parser.parse!

    @options
  rescue => err
    puts "Argument Error: #{err.message}"
    exit 1
  end

  def parser
    OptionParser.new do |opts|
      @opts = opts
      setup_options
    end
  end

  def setup_options
    begin_help_text
    setup_srcdir
    setup_destdir
    setup_cssdir
    setup_imagedir
    setup_jsdir
    setup_all_option
    show_help
  end

  def begin_help_text
    @opts.banner = "Usage:\n\t#{File.basename $PROGRAM_NAME} [options]"
    @opts.separator ''
  end

  def setup_srcdir
    @opts.on('-s', '--srcdir DIR',
             help_text('source directory', :dir)) do |dir|
      @options[:dir] = dir
    end
  end

  def setup_destdir
    @opts.on('-o', '--destdir DIR',
             help_text('root destination directory', :dest)) do |dir|
      @options[:dest] = dir
    end
  end

  def setup_cssdir
    @opts.on('-c', '--cssdir DIR',
             help_text('relative CSS directory', :cssdir)) do |dir|
      @options[:cssdir] = dir
    end
  end

  def setup_imagedir
    @opts.on('-i', '--imagedir DIR',
             help_text('relative images directory', :idir)) do |dir|
      @options[:idir] = dir
    end
  end

  def setup_jsdir
    @opts.on('-j', '--jsdir DIR',
             help_text('relative Javascript directory', :jsdir)) do |dir|
      @options[:jsdir] = dir
    end
  end

  def setup_all_option
    @opts.on('-a', '--all',
             'Copy all files (Default: changed only)') do
      @options[:all] = true
    end
  end

  def show_help
    @opts.on_tail('-h', '--help', 'Show this help') do
      puts @opts
      exit
    end
  end

  private

  def help_text(text, option_key = nil)
    default_text = option_key ? " (Default: #{@options[option_key]})" : ''

    "Set the #{text}#{default_text}"
  end
end
