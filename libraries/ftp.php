<?php namespace Hybrid;

/**
 * FTP Class based from Simple FTP Class
 * 
 * @package     FTP
 * @author      Shay Anderson 05.11
 * @link        http://www.shayandeerson.com/php/simple-ftp-class-for-php.htm
 * @license     GPL License <http://www.gnu.org/licenses/gpl.html>
 * 
 */

use \Log;

class FTP {
	/**
	 * FTP Host.
	 *
	 * @var  string
	 */
	protected $host = null;
	
	/**
	 * FTP Port.
	 *
	 * @var  int
	 */
	protected $port = 21;

	/**
	 * FTP User.
	 *
	 * @var  string
	 */
	protected $user = null;

	/**
	 * FTP Password.
	 *
	 * @var  string
	 */
	protected $password = null;

	/**
	 * FTP Stream
	 *
	 * @var  resource_id
	 */
	protected $stream = null;

	/**
	 * FTP timeout.
	 *
	 * @var  int
	 */
	protected $timeout = 90;

	/**
	 * FTP passive mode flag
	 *
	 * @var  bool
	 */
	protected $passive = false;

	/**
	 * SSL-FTP connection flag.
	 *
	 * @var  bool
	 */
	protected $ssl = false;

	/**
	 * System type of FTP server.
	 *
	 * @var  string
	 */
	protected $system_type;

	/**
	 * Make a new FTP instance
	 *
	 * @static
	 * @access public
	 * @param  array    $config
	 * @return FTP
	 */
	public static function make($config = array())
	{
		return new static($config);
	}

	/**
	 * Initialize connection params
	 *
	 * @access public	
	 * @param  array    $config
	 * @return void
	 */
	public function __construct($config = array())
	{
		if ( ! empty($config)) $this->setup($config);
	}

	/**
	 * Configure FTP.
	 *
	 * @access public
	 * @return void
	 */
	public function setup($config = array())
	{
		$host = isset($config['host']) ? $config['host'] : null;

		if (preg_match('/^(ftp|sftp):\/\/([a-zA-Z0-9\.\-_]*):?(\d{1,4})$/', $host, $matches))
		{
			$config['host'] = $matches[2];
			$config['ssl']  = ($matches[1] === 'sftp' ? true : false);

			if (isset($matches[3])) $config['port'] = $matches[3];
		}
	
		foreach ($config as $key => $value)
		{
			if ( ! property_exists($this, $key)) continue;

			$this->{$key} = $value;
		}
	}

	/**
	 * Change current directory on FTP server.
	 *
	 * @access public
	 * @param  string   $directory
	 * @return bool
	 */
	public function cd($directory)
	{
		if ( ! @ftp_chdir($this->stream, $directory))
		{
			throw new FTP\RuntimeException("Failed cd to [{$directory}].");
		}

		return true;
	}

	/**
	 * Get current directory path.
	 *
	 * @access public
	 * @return string
	 */
	public function pwd()
	{
		return @ftp_pwd($this->stream);
	}

	/**
	 * Download file from FTP server.
	 *
	 * @access public
	 * @param  string $remote_file
	 * @param  string $local_file
	 * @param  int $mode
	 * @return bool
	 */
	public function get($remote_file, $local_file, $mode = FTP_ASCII)
	{
		if ( ! @ftp_get($this->stream, $local_file, $remote_file, $mode))
		{
			throw new FTP\RuntimeException("Failed to download file [{$remote_file}].");
		}

		return true;
	}

	/**
	 * Upload file to FTP server.
	 *
	 * @access public
	 * @param  string $local_file
	 * @param  string $remote_file
	 * @param  int $mode
	 * @return bool
	 */
	public function put($local_file, $remote_file, $mode = FTP_ASCII)
	{
		if ( ! @ftp_put($this->stream, $remote_file, $local_file, $mode))
		{
			throw new FTP\RuntimeException("Failed to upload file [{$local_file}].");
		}

		return true;
	}

	/**
	 * Rename file on FTP server.
	 *
	 * @access public
	 * @param  string $old_name
	 * @param  string $new_name
	 * @return bool
	 */
	public function rename($old_name, $new_name)
	{
		if ( ! @ftp_rename($this->stream, $old_name, $new_name))
		{
			throw new FTP\RuntimeException("Failed to rename file [{$old_name}].");
		}

		return true;
	}

	/**
	 * Delete file on FTP server.
	 * 
	 * @access public
	 * @param  string   $remote_file
	 * @return bool
	 */
	public function delete($remote_file)
	{
		if ( ! @ftp_delete($this->stream, $remote_file))
		{
			throw new FTP\RuntimeException("Failed to delete file [{$remote_file}].");
		}

		return true;
	}

	/**
	 * Set file permissions.
	 *
	 * @access public
	 * @param  string   $remote_file
	 * @param  int      $permissions    For example: 0644
	 * @return bool
	 * @throws RuntimeException         If unable to chmod $remote_file
	 */
	public function chmod($remote_file, $permission = 0644)
	{
		if ( ! @ftp_chmod($this->stream, $permission, $remote_file))
		{
			throw new FTP\RuntimeException("Failed chmod for [{$remote_file}].");
		}

		return true;
	}

	/**
	 * Get list of files/directories on FTP server.
	 *
	 * @access public
	 * @param  string $directory
	 * @return array
	 */
	public function ls($directory)
	{
		if ( ! ($list = @ftp_nlist($this->stream, $directory)))
		{
			throw new FTP\RuntimeException("Failed to get directory list for [{$directory}].");
		}

		return is_array($list) ? $list : array();
	}

	/**
	 * Create directory on FTP server.
	 *
	 * @access public
	 * @param  string $directory
	 * @return bool
	 */
	public function mkdir($directory) 
	{
		if ( ! @ftp_mkdir($this->stream, $directory))
		{
			throw new FTP\RuntimeException("Failed to create directory [{$directory}].");
		}

		return true;
	}

	/**
	 * Remove directory on FTP server.
	 *
	 * @access public
	 * @param  string $directory
	 * @return bool
	 */
	public function rmdir($directory)
	{
		if ( ! @ftp_rmdir($this->stream, $directory))
		{
			throw new FTP\RuntimeException("Failed to remove directory [{$directory}].");
		}

		return true;
	}

	/**
	 * Connect to FTP server.
	 *
	 * @access public
	 * @return bool
	 * @throws RuntimeException If unable to connect to FTP server.
	 */
	public function connect()
	{
		if (is_null($this->host)) return ;

		if ($this->ssl and function_exists('ftp_ssl_connect'))
		{
			if ( ! ($this->stream = @ftp_ssl_connect($this->host, $this->port, $this->timeout))) 
			{
				throw new FTP\ServerException(
					"Failed to connect to [{$this->host}] (SSL Connection)."
				);
			}
		}
		elseif ( ! ($this->stream = @ftp_connect($this->host, $this->port, $this->timeout)))
		{
			throw new FTP\ServerException("Failed to connect to [{$this->host}].");
		}

		return $this->login();
	}

	/**
	 * Login to FTP server.
	 *
	 * @access protected
	 * @return bool
	 * @throws RuntimeException If failed to login to FTP server.
	 */
	public function login()
	{
		if ( ! (@ftp_login($this->stream, $this->user, $this->password)))
		{
			throw new FTP\ServerException("Failed FTP login to [{$this->host}].");
		}

		// Set passive mode.
		ftp_pasv($this->stream, (bool) $this->passive);

		// set system type
		$this->system_type = ftp_systype($this->stream);
		
		return true;
	}

	/**
	 * Close FTP connection.
	 *
	 * @access public
	 * @return void
	 * @throws RuntimeException If unable to close connection.
	 */
	public function close()
	{
		if ($this->stream) ftp_close($this->stream);
		
		$this->stream = false;
	}

	/**
	 * Check FTP connection status.
	 *
	 * @access public
	 * @return bool
	 */
	public function connected()
	{
		return ( ! is_null($this->stream));
	}
}
