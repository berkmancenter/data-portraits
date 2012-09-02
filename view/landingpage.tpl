{include file="_header.tpl"}
    <table id="mainTable">
        <tr>
            <td class="column_main">
                <div class="info blue_back">
                    <h2>About the Project</h2>
                    <p>{include file="_about.tpl"}</p>
                </div>
            </td>
            <td class="column_separator"></td>
            <td class="column_main">
                <div class="info center">
                    <a class="center_image" href="{$site_root_path}crawler/login.php">
                        <img width="300" src="{$site_root_path}assets/images/darker.png" />
                    </a>
                </div>
            </td>
        </tr>
    </table>
{include file="_footer.tpl"}