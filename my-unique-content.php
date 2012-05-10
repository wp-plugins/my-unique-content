<?php
/*
Plugin Name: My Unique Content
Plugin URI: http://jamesmartinez2011.wordpress.com/wordpress-plugins/
Description: This plugin is an SEO Tool that will convert all your content based from the specified words in a record to their corresponding synonyms. 
It will give you a unique content which will eventually help you boost your rankings and presence to Big G (Google).
Version: 1.0
Author: James Martinez
Donate url: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=TB7GJHE9R3KXS
License: GPL
*/

function ab_option_form(){
  global $ab_values;
  
  ob_start(); ?>
           <div class="wrap">  
              <h2>My Unique Content Settings</h2> 
                   
              
              <div style="border:2px dotted red; padding: 20px;">
              <span style="color:red; font-size:14px; font-weight: bold;">Is this plugin helpful? Is it worth ten dollars to you?
Consider helping me by donating some..</span>
              <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="TB7GJHE9R3KXS">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
                  </div>

              
            
              <form method="post" action="options.php">
                   <?php settings_fields( 'ab_settings_group' ); ?> 
                  
                  <h4><?php _e('Modify/Enter the words that needs to be replaced for your unique content.','ab_form'); ?></h4>
                    <i>Note:  Enter your custom words separated by new line in the list 
                        and their corresponding synonyms. Each line of words should match to their synonyms.
For example you added the word "common" in line 20 in your word list, you should 
add its synonym in the same line as well in your synonym list.
                    </i>
                  <table><tr>
                          <td>
                   
                         <label class="description" for="ab_values[search]"><?php _e('Words:','ab_form'); ?></label> <br/>
                         <textarea id="ab_values[search]" cols="50" rows="20" name="ab_values[search]"><?php echo $ab_values['search']?></textarea>
                    
                   </td>
                   <td>
                       
                         <label class="description" for="ab_values[replace]"><?php _e('Change to [Synonyms]:','ab_form'); ?></label> <br/>
                         <textarea id="ab_values[replace]" cols="50" rows="20" name="ab_values[replace]"><?php echo $ab_values['replace']?></textarea>
                                            
                   </td>
                      </tr>                       
                  </table>
                  <p class="submit">
                          <input type="submit" class="button-primary" value="<?php _e('Save Options','ab_form'); ?>" />
                  </p>
              </form>
          </div>
  <?php 
  echo ob_get_clean();
}


function ab_menu(){
   add_options_page('My Unique Content Options','My Unique Content Plugin','manage_options','my-unique-content','ab_option_form');
}


function ab_update(){
  register_setting('ab_settings_group','ab_values');
}
 


$ab_values =  get_option('ab_values');

if( empty($ab_values['search']) ){
$list1 = "refer
about
also
amazing
awesome
awful
bad
beautiful
begin
big
boring
but
change
choose
cool
definitely
easy
excellent
exciting
fast
finish
fun
funny
get
give
good
got
great
guy
happy
hard
help
hurt
important
interesting
and
after
later
soon
common
situated";
 

$list2 = "pertain
approximately
moreover
astonishing
impressive
dreadful
offensive
attractive
commence
huge
tedious
yet
alter
select
rad
unquestionably
effortless
superior
thrilling
swift
complete
pleasant
comical
obtain
donate
satisfactory
received
tremendous
man
pleased
complex
assist
injure
significant
absorbing
&
later
after
shortly
usual
located";
 

   $default = array("search" => $list1, "replace" => $list2 ) ;  
   update_option('ab_values', $default);  
} 



///alphabeticalize here//////////////////////////////////
$search  = explode("\n",$ab_values["search"]);
$replace = explode("\n",$ab_values["replace"]);
$replace_dummy = $replace;

$count = count($search);
for($i = 0; $i  < $count; $i++){
    $search[$i] = $search[$i].":::".$i;    
}
sort($search);

for($i = 0; $i  < $count; $i++){
    $tmp = explode(":::",$search[$i]);
    $search[$i] = $tmp[0];
    $j = $tmp[1];        
    $replace[$i] = $replace_dummy[$j];
}

//sort($replace);
$search = implode("\n", $search);
$replace = implode("\n", $replace);
$ab_values["search"] = $search;
$ab_values["replace"] = $replace;
///alphabeticalize here//////////////////////////////////

 


// Add settings link on plugin page//////////////////////
function your_plugin_settings_link($links) { 
  $settings_link = '<a href="options-general.php?page=my-unique-content">Settings</a>'; 
  array_unshift($links, $settings_link); 
  return $links; 
}
 
$plugin = plugin_basename(__FILE__); 




function ab_unique_content($str){  
   
    //gather links
    $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>"; 
    $link_count = 0;
    $links = array();
    if(preg_match_all("/$regexp/siU", $str, $matches, PREG_SET_ORDER)) {         
        foreach($matches as $match) {                      
                   $links[$link_count] = $match[2];
                   $str = str_replace($links[$link_count],"__".$link_count."__",$str);
                   $link_count ++;
                    //// $match[2] = link address 
                    //// $match[3] = link text 
            } 
     }
    
    
  global $ab_values;
  $search = explode("\n",$ab_values["search"]);
  $replace = explode("\n",$ab_values["replace"]);  
  
  $count  = count($search);
  $output = $str;
  for($i = 0; $i < $count; $i++){
      $output = str_replace(" ".trim($search[$i])." "," ".trim($replace[$i])." ", $output);
  }
  
  //return original links 
  if($link_count > 0 ){
    for($i = 0 ; $i < $link_count; $i++){
      $output = str_replace("__".$i."__", $links[$i] , $output);
    }
  }

  return  $output;  
}

  

add_action('admin_menu','ab_menu');
add_action('admin_init','ab_update');
add_filter( 'the_excerpt', 'ab_unique_content' );
add_filter( 'the_content', 'ab_unique_content' );
add_filter("plugin_action_links_$plugin", 'your_plugin_settings_link' );
  
?>