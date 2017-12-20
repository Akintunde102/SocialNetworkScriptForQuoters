				<?php
				echo '<div class="lt">';
	echo '<div class="cg"  role="tabpanel" data-example-id="togglable-tabs" style="position: fixed;left: 2.1%;top: 9%;width: 16%;font-size: 15px;">
                   <a href="#tab_content1" role="tab" id="home-tab">Categories</a>
		    <a href="#tab_content2" role="tab" id="profile-tab">Authors</a>
                </div>';
		echo '<div id="myTabContent" class="tab-content">';
                  echo '<div role="tabpanel" class="tab-pane fade active in" id="tab_content1" style=" margin-top:30%;">';
echo $wp->listout('categories');
echo '</div>';
      echo '<div role="tabpanel" class="tab-pane fade" id="tab_content2">';
	  echo $wp->listout('authors');
	  echo '</div>';
	    echo '</div>';
	echo '</div>';
?>


