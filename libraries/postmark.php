<?php
/**
 * A Laravel bundle for Postmark.
 *
 * @package    Postmark
 * @author     Terry Matula (http://terrymatula.com), based on a library from Matthew Loberg (http://mloberg.com) and Drew Johnston (http://drewjoh.com)
 * @link       https://github.com/matula/laravel-postmark
 * @license    Dbad License
 */

class Postmark {

	private $api_key;
	private $attachment_count = 0;
	private $data = array();

	public function __construct()
	{	
		$this->api_key = Config::get('postmark::options.api_key');
		$this->data['From']    = Config::get('postmark::options.from');
	}

	public function send()
	{
		$headers = array(
			'Accept: application/json',
			'Content-Type: application/json',
			'X-Postmark-Server-Token: '.$this->api_key
		);

		$return = array();
		
		$ch = curl_init('http://api.postmarkapp.com/email');
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->data));
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	
		$ch_return  = curl_exec($ch);
		$curl_error = curl_error($ch);
		$http_code  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		
		curl_close($ch);
		
		// do some checking to make sure it sent
		if($http_code !== 200)
		{
			$return['error'] = TRUE;
			$return['message'] = 'HTTP Code: ' . $http_code . ' >> Error: ' . $curl_error;
		} 
		else
		{
			$return['error'] = FALSE;
			$return['message'] = $ch_return;
		}
			return $return;
	}

	public function from_name($from)
	{
		$this->data['From'] = $from . ' <'  . $this->data['From'] . '>';
		return $this;
	}

	public function to($to)
	{
		$this->data['To'] = $to;
		return $this;
	}
	
	public function cc($cc)
	{
		$this->data['Cc'] = $cc;
		return $this;
	}
	
	public function bcc($bcc)
	{
		$this->data['Bcc'] = $bcc;
		return $this;
	}

	public function reply($reply_to)
	{
		$this->data['ReplyTo'] = $reply_to;
		return $this;
	} 
		
	public function subject($subject)
	{
		$this->data['Subject'] = $subject;
		return $this;
	}

	public function html_body($html)
	{
		$this->data['HtmlBody'] = $html;
		return $this;
	}

	public function txt_body($msg)
	{
		$this->data['TextBody'] = $msg;
		return $this;
	}

	public function tag($tag)
	{
		$this->data['Tag'] = $tag;
		return $this;
	}
	
	public function attachment($name, $content, $content_type)
	{
		$this->data['Attachments'][$this->attachment_count]['Name']		= $name;
		$this->data['Attachments'][$this->attachment_count]['ContentType']	= $content_type;
		
		// Check if our content is already base64 encoded or not
		if( ! base64_decode($content, true))
			$this->data['Attachments'][$this->attachment_count]['Content']	= base64_encode($content);
		else
			$this->data['Attachments'][$this->attachment_count]['Content']	= $content;
		
		// Up our attachment counter
		$this->attachment_count++;
		
		return $this;
	}

}