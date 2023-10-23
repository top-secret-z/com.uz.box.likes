<ul class="sidebarItemList">
    {foreach from=$boxUserList item=$boxUser}
        <li class="box32">
            <a href="{link controller='User' object=$boxUser}{/link}" aria-hidden="true">{@$boxUser->getAvatar()->getImageTag(32)}</a>

            <div class="sidebarItemTitle">
                <h3>{user object=$boxUser}</h3>
                <small>{lang}wcf.user.uzboxLikes.likes{/lang}</small>
            </div>
        </li>
    {/foreach}

    {if $lasts|count}
        <li>{lang}wcf.user.uzboxLikes.last{/lang}</li>

        {foreach from=$lasts item=$boxUser}
            <li class="box32">
                <a href="{link controller='User' object=$boxUser}{/link}" aria-hidden="true">{@$boxUser->getAvatar()->getImageTag(32)}</a>

                <div class="sidebarItemTitle">
                    <h3>{user object=$boxUser}</h3>
                    <small>{lang}wcf.user.uzboxLikes.likes{/lang}</small>
                </div>
            </li>
        {/foreach}
    {/if}
</ul>
