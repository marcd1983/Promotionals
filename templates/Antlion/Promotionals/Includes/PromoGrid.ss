<div class="grid-x grid-padding-x grid-padding-y large-up-4">
    <% loop $PromoList.Sort(SortOrder) %>
        <div class="element__promos__item cell">
            <% include Antlion/Promotionals/Includes/PromoCard %>
        </div>
    <% end_loop %>
</div>