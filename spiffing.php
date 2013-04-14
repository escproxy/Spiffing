<?php
/**
 * The Queen's Stylesheet
 *
 * Brits, take control of the web. Spiffing allows you to write your CSS and stylesheets 
 * in conformance to proper British English (also known as correct English) grammar and 
 * spelling regulations.
 *
 *
 * Example of use:
 * 
 * Take your typical string CSS which you want to be be parsed by
 * Her Majesty the Queen's Spiffing CSS parser:
 *
 * $royalty	= "body {"
 *		. "	background-photograph: url('photographs/my_corgies.png');"
 *		. "}";
 *
 * Class is never to be forgotten when compiling such royal stylesheets, therefore, it is
 * with utter urgence that one begin to compile like so:
 *
 * $css 	= new spiffing($royalty, false);
 *
 * As you can see, one has to use but the finest, and simplest, techniques in order to achieve
 * their goal. Finally, we shall output our stylesheet for our friends across the pond:
 *
 * $css->process();
 *
 * Perfect!
 * 
 * @authors 	@kapooht & @idiot
 * @license 	☺ License (http://licence.visualidiot.com)
 *
 */
	class spiffing
	{
		/**
		 * The dictionary which has been made into a more accessible array.
		 *
		 * @var		array
		 * @access	public
		 */
		public $dictionary 		= array(
			// Queen's English	// Primitive English from our stateside friends from across the pond.
			'colour'		=> 'color',
			'grey'			=> 'gray',
			'!please'		=> '!important',
			'transparency'		=> 'opacity',
			'centre'		=> 'center',
			'plump'			=> 'bold',
			'photograph'		=> 'image',
			'capitalise'		=> 'capitalize'
		);
		/**
		 * The 'fail gracefully' variable allows the user to load the 'NOT FOUND' header if, well, nothing is found.
		 * If, however, this is set to TRUE, then nothing will be shown at all.
		 *
		 * @var 	boolean
		 * @access 	public
		 */
		public $fail_gracefully		= FALSE;
		/**
		 * Did the operation fail? We shall see.
		 * This should be set to FALSE by default.
		 *
		 * @access	private
		 * @var		boolean
		 */
		private $we_failed		= FALSE;
		/**
		 * The URL hook. Check out the .htaccess for further customisation.
		 *
		 * @access	private
		 * @var		string
		 */
		private $hook			= 'file=';
		/**
		 * The file variable
		 *
		 * @access 	public
		 * @var		string
		 */
		public $file;
		/**
		 * Perfectly British CSS right here
		 *
		 * @access 	public
		 * @var 	string
		 */
		public $css;
		/**
		 * Constructor
		 *
		 * @access 	public
		 * @param 	string	Raw CSS
		 * @param 	string	Should requesting the class automatically return CSS? Default TRUE;
		 * @return 	void
		 */
		function __construct( $raw = '', $auto_output = true )
		{
			if ( !empty( $raw ))
			{
				$this->css = $raw;
			}
			else if ( isset( $_SERVER ) )
			{
				// Santise the string.
				$this->file 	= dirname( __FILE__ )
						. '/' . str_replace( $this->hook, '',
						filter_input( INPUT_GET, 'file', FILTER_SANITIZE_STRING ) );
				if( !file_exists( $this->file ) or !is_readable( $file ) )
				{
					$this->not_found();
				}
				$this->css = file_get_contents( $this->file );
			}
			else
			{
				// No spiffing CSS was found.
				$this->not_found();
			}
			if( $auto_output == true )
			{
				$this->process();
			}
		}
		/**
		 * Magic really.
		 *
		 * @access 	public
		 * @param	void
		 * @return 	string
		 */
		public function process()
		{
			// The finished CSS.
			$processed		= '';
			// The array which will hold all found CSS attributes to be repalced.
			$replacements		= array();
			// The magic pattern which finds ONLY attributes.
			$pattern		= '/(?:(?:\s|\t)*|\;)([\w-]*):/i';
			// One should begin by searching the CSS for exlusive Britishness.
			preg_match_all( $pattern, $this->css, $matches );
			foreach( $matches[1] as $index => $value )
			{
				// Let's run through the Queen's dictionary for every found term.
				foreach( $this->dictionary as $british => $primitive )
				{
					// Did we find some Britishness?
					if( strpos( $value, $british ) !== FALSE )
					{
						// We don't want overlapse - now we do not have an as big foreach to get through.
						$replacements[$value] = str_ireplace($british, $primitive, $value);
					}
				}
			}
			// Now that we have the attributes to replace, let us begin...
			foreach( $replacements as $search => $replace ) {
				$processed = str_ireplace( $search, $replace, $this->css );
			}
			// Set the CSS header.
			$this->set_header('Content-Type: text/css');
			echo $processed;
			exit;
		}
		/**
		 * Set a custom header
		 *
		 * @access 	private
		 * @param	string
		 * @return 	void
		 */
		private function set_header($header)
		{
			//  Safely set the header
			if( !headers_sent() )
			{
				header( $header );
			}
		}
		/**
		 * Create a 404 function, since we do that a lot.
		 *
		 * @access 	private
		 * @param	void
		 * @return 	void
		 */
		private function not_found()
		{
			// Since we got called, we failed.
			$this->we_failed = TRUE;
			// Let's check if we should do something about that.
			if ( $this->fail_gracefully == FALSE )
			{
				//  Set the header
				$this->set_header( 'HTTP/1.0 404 Not Found' );
				//  And stop execution. We don't need no content.
				exit;
			}
		}
		/**
		 * A public access of the failure variable.
		 *
		 * @access 	public
		 * @param 	void
		 * @return 	boolean
		 */
		public function did_we_fail()
		{
			return $this->we_failed;
		}
		/**
		 * Deconstructor
		 */
		function __deconstruct()
		{
			$this->not_found();
		}
	}
?>
