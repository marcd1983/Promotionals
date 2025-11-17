<div class="main grid-container $PageWidth" role="main">
	<% include TitleBar %>
		<% if $Content %>
		<div class="grid-x content">
			<div class="cell">
				$Content
			</div>
		</div>
		<% else %>
			$ElementalArea
		<% end_if %>
  <% if $Promos %>
	<div class="grid-promos grid-x grid-padding-x grid-padding-y large-up-4">
    <% loop $Promos %>
      <div class="cell">
        <% include PromoCard %>
      </div>
        <% end_loop %>
      </div>
    <% end_if %>
</div>

  