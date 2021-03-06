<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Messages Controller.
 * View SMS Messages Received Via FrontlineSMS
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com>
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Admin Messages Controller
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL)
 */

class Feeds_Controller extends Admin_Controller
{

	
	function __construct()
	{
		parent::__construct();

	//	$this->template->this_page = 'Add Feeds';
		//$this->template->content->form_saved = false;
	}

	public function index($feedtype = 'rss2') 
	{
		
		
		$this->template->this_page = 'feed_add';
	
		$this->template->content = new View('admin/feeds');
		// setup and initialize form field names
	/*	$cat1fields1 = array('1feed_url1' => '',
												'1weight1' => 1,
												'1feed_category1' => 1);		

		*/
		$form = array();
		$num_of_fields_persection = 6;
		
		$settings = ORM::factory('settings', 1);
				
		$form["EMAIL"] = $settings->site_email;
		$form["SMS"] = $settings->sms_no1;		
		$hashtags = explode(',',$settings->twitter_hashtags);

				
		 	$cat_counter = 0;
			$cat_max = count($hashtags);
				
			for($i=1;$i <= $num_of_fields_persection; $i++)
			{ 
				if( $cat_counter < $cat_max ) 
				{
						$form["hashtag".$i] = $hashtags[$cat_counter];								
						$cat_counter++;
				}
				else
				{
						$form["hashtag".$i] = "";
				}
			}
						
				
		
		
		$categories = ORM::factory('category')->where('category_visible = 1')->orderby('id')->find_all();								
		foreach($categories as $cat)
		{
				$prev_feeds = ORM::factory('feed')->where('category_id',$cat->id)->find_all();
				
				$cat_counter = 0;
				$cat_max = count($prev_feeds);				
				for($i=1; $i<= $num_of_fields_persection;$i++)
				{ 
					//this section gets the information from the database and sign it to the right fields.
					if( $cat_counter < $cat_max &&  $prev_feeds[$cat_counter]->category_id == $cat->id) 
					{
							$pre_feed	=	$prev_feeds[$cat_counter];
							$form[$cat->category_title."ID".$i] = $pre_feed->id;
							$form[$cat->category_title."feed_url".$i] = $pre_feed->feed_url;
							$form[$cat->category_title."weight".$i] = $pre_feed->weight ;
							$form[$cat->category_title."feed_category".$i] = $pre_feed->category_id;
							
							$cat_counter++;
					}
					else
					{
							$form[$cat->category_title."ID".$i] = 0;
							$form[$cat->category_title."feed_url".$i] = '';
							$form[$cat->category_title."weight".$i] = 0 ;
							$form[$cat->category_title."feed_category".$i] = $cat->id;
					}
				}							
		}
		
				//copy the form as errors, so the errors will be stored with keys corresponding to the form field names
		$errors = $form;
		$form_error = FALSE;

				// check, has the form been submitted, if so, setup validation
		if ($_POST)
		{
					// Instantiate Validation, use $post, so we don't overwrite $_POST fields with our own things
					$post = Validation::factory(array_merge($_POST,$_FILES));
					
					 //  Add some filters
					$post->pre_filter('trim', TRUE);

					if ($post->validate())
					{
							foreach($categories as $cat)
							{
									for($i=1; $i<=$num_of_fields_persection ; $i++)
									{
											if( isset($_POST[$cat->category_title."feed_url".$i]) && !empty($_POST[$cat->category_title."feed_url".$i]) )
											{
														$feed_url = $_POST[$cat->category_title."feed_url".$i];
														$feed_weight = $_POST[$cat->category_title."weight_hf".$i];
														
														$this->_save_feed($_POST[$cat->category_title."ID".$i],
																						$feed_url,
																						$cat->id,
																						$feed_url,
																						isset($_POST[$cat->category_title."weight".$i])?100:
																						($feed_weight == 100?0:$feed_weight)
																						);																	
																					
											}
											else if(isset( $_POST[$cat->category_title."ID".$i]) && $_POST[$cat->category_title."ID".$i] != 0)
											{	 // delete what the user has deleted.
													$db = new Database();
													$db->query("Delete from feed where id=".$_POST[$cat->category_title."ID".$i] );
											}
									}												
							}			
						//	exit(0);
								
								$hashtags = isset($_POST["hashtag1"])	&& !empty($_POST["hashtag1"])? $_POST["hashtag1"] : '' ;
					  		for($i=2;$i<$num_of_fields_persection  ;$i++)
							  {					
										if (isset($_POST["hashtag".$i])	&& !empty($_POST["hashtag".$i]))
										{	
												$hashtags .=	",".$_POST["hashtag".$i]	;													
										}		
								}
								//save the hashtags in the settings table.		
								$settings = new Settings_Model(1);
								$settings->twitter_hashtags = 	$hashtags;
								$settings->site_email = $_POST["EMAIL"];
	 							$settings->sms_no1 =	$_POST["SMS"];
								$settings->save();
							
								$this->template->content->form_saved = true;
								$this->template->content->form_action = "Added / Updated"	;	
										
					//	exit(0);
							  url::redirect("/admin/feeds");
						}
						else
						{				
									// populate the error fields, if any
									$errors = arr::overwrite($errors, $post->errors('report'));
									$form_error = TRUE;
					
				  	}
				}
				
				$this->template->content->errors = $errors;
			 	$this->template->content->form_error = $form_error;
				$this->template->content->form_saved = false;
				$this->template->content->form = $form;
				// Count categories to determine column length
			
					
	}
	
	// STEP 2: SAVE Feed
	private function _save_feed($feed_id,$feed_url,$feed_category,$feed_name="none",$weight)
	{
	
				$feedname = $feed_name == "none" ? $feed_url :$feed_name ;
				
				if(isset($feed_url) &&  !empty($feed_url) && $feed_url != '' && $feed_category > 0 )
				{
						$feed = new Feed_Model();
							//if unique url then create new else update old.
						$numItems = ORM::factory('feed')->where('feed_url',$feed_url)->count_all();
						if ($numItems == 0 && $feed_id == 0 )	
						{		
								$feed->feed_name = $feedname ;
								$feed->feed_url = $feed_url;
								$feed->weight =  $weight ;
								$feed->category_id = $feed_category;	
								$feed->save();
								
						}else if($feed_id != 0 )
						{
								$db = new Database();
								$sql = " UPDATE feed SET feed_url = '".$feed_url."' , feed_name = '".
												$feedname."' , weight= ".$weight."	";
								$sql .= " WHERE id = ".$feed_id ;
							//				echo $sql."<br/>";						
								$Result = $db->query($sql);
						}
						$send = notifications::notify_admins(
													"[".Kohana::config('settings.site_name')."] ".
														Kohana::lang('notifications.admin_new_report.subject'),
													Kohana::lang('notifications.admin_new_report.message')
														."\n\n'".strtoupper($feed->feed_name)."'"
														."\n".$feed->feed_url
															);
															
				
				}
	}
	
}
