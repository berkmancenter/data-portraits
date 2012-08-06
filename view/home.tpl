{include file="_header.tpl"}
    <form method="post" action="{$site_root_path}crawler/crawl.php" id="search_box">
	<div class="wrapper">
		<input type="text" id="username" name="username"
                    placeholder="Enter Username To Create Data Portrait" />
		<button type="submit" class="search_btn">
                    <img src="{$site_root_path}assets/images/search_icon.png"
                        title="Search" />
                </button>
	</div>
    </form>
{include file="_footer.tpl"}