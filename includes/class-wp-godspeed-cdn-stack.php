<?php

/**
 * The main CDN class.
 *
 * @link       https://wpgodspeed.io
 * @since      1.0.0
 *
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/includes
 */

/**
 * The main CDN class.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    WP_Godspeed
 * @subpackage WP_Godspeed/includes
 * @author     WP Godspeed <hello@wpgodspeed.io>
 */
if ( ! class_exists( 'WP_Godspeed_CDN_Stack_Plugin' ) )
{
	class WP_Godspeed_CDN_Stack_Plugin extends WP_Godspeed_Admin {

		function addaction( $h )
		{
			$p    = 10;
			$args = 999;
			$m    = $this->sanitize_method( $h );
			$b    = func_get_args();
			unset( $b[0] );
			foreach ( (array) $b as $a )
			{
				if ( is_int( $a ) )
				{
					$p = $a;
				}
				else
				{
					$m = $a;
				}
			}
			return add_action( $h, array( $this, $m ), $p, $args );
		}

		function addfilter( $f )
		{
			$p    = 10;
			$args = 2;
			$m    = $this->sanitize_method( $f );
			$b    = func_get_args();
			unset( $b[0] );
			foreach ( (array) $b as $a )
			{
				if ( is_int( $a ) )
				{
					$p = $a;
				}
				else
				{
					$m = $a;
				}
			}
			return add_filter( $f, array( $this, $m ), $p, $args );
		}

		private function sanitize_method( $m )
		{
			return str_replace( array( '.', '-' ), array( '_DOT_', '_DASH_' ), $m );
		}
	}
}

class WP_Godspeed_CDN_Stack extends WP_Godspeed_CDN_Stack_Plugin {

	public $site_domain;
	public $cdn_domain;
	public $root_url;
	public $root_cdn_url;
	public $uploads_only;
	public $mime_types;
	public $extensions;
	public $cdn_debug;

	public function __construct()
	{
		$this->addaction( 'plugins_loaded' );
	}

	public function plugins_loaded()
	{
		if ( get_option( $this->option_name . '_cdn_enabled' ) == 1 )
		{
			$this->addaction( 'init' );
		}
	}

	public function init()
	{
		$cdn_debug          = get_option( $this->option_name . '_cdn_debug' );
		$this->cdn_debug    = ( $cdn_debug == 1 ? TRUE : FALSE );
		$this->uploads_only = TRUE;
		$this->mime_types   = array(
			'jpe?g?', 'gif', 'png', 'bmp', 'tiff?', 'ico', 'mp3',
			'mov', 'mp4', 'weba?m?p?', 'avi', 'm4a?v?', 'mpe?g?',
			'wav', 'wma', 'aac' ,'ogg', '3gp?2?', 'txt', 'exe',
			'zip', 'pdf', 'docx?', 'pptx?', 'xlsx?', 'gz', 'gzip',
			'j?r?ar', 'tar', '7z'
			//'woff2?', 'eot', 'otf', 'ttf', 'ttc', 'svg'
			//fonts won't work if cors headers aren't set by the origin
		);
		$this->extensions   = apply_filters( $this->option_name . '_cdn_extensions', $this->mime_types );
		$this->site_domain  = parse_url( get_bloginfo( 'url' ), PHP_URL_HOST );
		$this->cdn_domain   = get_option( $this->option_name . '_subdomain' ) . '.godspeedcdn.com';
		$this->root_url     = 'https://' . $this->site_domain;
		$this->root_cdn_url = 'https://' . $this->cdn_domain;

		if ( ! is_admin() )
		{
			$this->addaction( 'send_headers', 'header_directives' );
			$this->addaction( 'template_redirect' );
			if ( $this->uploads_only )
			{
				$this->addaction( $this->option_name . '_cdn_content', 'filter_img_srcsets' );
				$this->addaction( $this->option_name . '_cdn_content', 'filter_uploads_only' );
			}
			else
			{
				$this->addaction( $this->option_name . '_cdn_content', 'filter' );
			}
			if ( $this->cdn_debug )
			{
				$this->addaction( $this->option_name . '_cdn_content', 'debug' );
			}
		}
	}

	public function header_directives( )
	{
		header('X-Godspeed-CDN: enabled');
	}

	public function filter_uploads_only( $content )
	{
		$upload_dir = wp_upload_dir();
		$upload_dir = $upload_dir['baseurl'];
		$domain     = preg_quote( parse_url( $upload_dir, PHP_URL_HOST ), '#' );
		$path       = parse_url( $upload_dir, PHP_URL_PATH );
		$preg_path  = preg_quote( $path, '#' );
		return preg_replace( "#([=\"'])(https?:)?(\/\/){$domain}?\/(wp\-content\/)(themes|uploads)(\/(?:(?!\\1).)+)\.(" . implode( '|', $this->extensions ) . ")(\?ver=[0-9\.]+)?(\?[0-9]+)?([\s\"'])#", '$1$2$3' . $this->cdn_domain . '/wp-content/$5$6.$7$8$9$10', $content );
	}

	public function filter( $content )
	{
		return preg_replace( "#=([\"'])(https?://{$this->site_domain})?/([^/](?:(?!\\1).)+)\.(" . implode( '|', $this->extensions ) . ")(\?((?:(?!\\1).)+))?\\1#", '=$1//' . $this->cdn_domain .'/$3.$4$5$1', $content );
	}

	public function filter_img_srcsets( $content )
	{
		$srcset_regex = '#<img[^\>]*[^\>\S]+srcset=[\'"](' . $this->root_url . '(?:([^"\'\s,]+)(' . implode( '|', $this->extensions ) . ')\s*(?:\s+\d+[wx])(?:,\s*)?)+)["\'][^>]*?>#';
		$content      = preg_replace_callback( $srcset_regex, array( $this, 'srcset_rewrite' ), $content );
		return $content;
	}

	public function srcset_rewrite( $match )
	{
		$image_tag    = empty( $match[0] ) ? false : $match[0];
		$srcset_field = empty( $match[1] ) ? false : $match[1];
		if ( empty( $srcset_field ) )
		{
			return $image_tag;
		}
		$srcset_images       = array();
		$srcset_images_count = preg_match_all( '#' . quotemeta( $this->root_url ) . '(?:([^"\'\s,]+)\s*(?:\s+\d+[wx])(?:,\s*)?)#', $image_tag, $srcset_images );
		$srcset_images_sizes = empty( $srcset_images[0] ) ? false : $srcset_images[0];
		$srcset_images_paths = empty( $srcset_images[1] ) ? false : $srcset_images[1];
		if ( empty( $srcset_images_paths ) )
		{
			return $image_tag;
		}
		foreach ( $srcset_images_paths as $k => $original_path )
		{
			$path                 = $this->get_rewrite_path( $original_path );
			$cdn_path             = $this->root_cdn_url . $path;
			$srcset_images[0][$k] = str_replace( $this->root_url . $original_path, $cdn_path, $srcset_images[0][$k] );
		}
		$image_tag = str_replace( $srcset_field, implode( ' ', $srcset_images[0] ), $image_tag );
		return $image_tag;
	}

	private function get_rewrite_path( $path )
	{
		global $blog_id;
		if ( is_multisite() && ! is_subdomain_install() && $blog_id !== 1 )
		{
			$bloginfo = $this->get_this_blog_details();
			if ( ( strpos( $path, $bloginfo->path ) === 0 ) && ( strpos( $path, $bloginfo->path . 'files/' ) !== 0 ) )
			{
				$path = '/' . substr( $path, strlen( $bloginfo->path ) );
			}
		}
		return $path;
	}

	public function debug( $content )
	{
		$content .= "<!-- start wpgodspeed debug -->\n";
		$content .= "<!-- site_domain: $this->site_domain -->\n";
		$content .= "<!-- cdn_domain: $this->cdn_domain -->\n";
		$content .= "<!-- option_name: $this->option_name -->\n";
		$content .= "<!-- end wpgodspeed debug -->\n";
		return $content;
	}

	public function template_redirect()
	{
		ob_start( array( $this, 'ob' ) );
	}

	public function ob( $contents )
	{
		return apply_filters( $this->option_name . '_cdn_content', $contents, $this );
	}


}
