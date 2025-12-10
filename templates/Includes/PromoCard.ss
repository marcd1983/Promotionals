<div class="card promo">
  <% if $Image %>
    <% if $Link %><a href="$Link" title="$Title"<% if $Link.OpenInNew %> target="_blank" rel="noopener noreferrer"<% end_if %>><% end_if %>
    <% if $Top.Lazy %>
      <picture>
        <source media="(min-width:1024px)" data-srcset="<% if function_exists('FocusFill') %>$Image.FocusFill(600,600).URL<% else %>$Image.Fill(600,600).URL<% end_if %>">
        <source media="(max-width:1023px)" data-srcset="<% if function_exists('FocusFill') %>$Image.FocusFill(600,600).URL<% else %>$Image.Fill(600,600).URL<% end_if %>">
        <img class="swiper-lazy"
             data-src="<% if function_exists('FocusFill') %>$Image.FocusFill(600,600).URL<% else %>$Image.ScaleMaxWidth(600).URL<% end_if %>"
             alt="$Image.Title.ATT" width="600" height="600"
             style="width:100%;height:auto;">
      </picture>
      <div class="swiper-lazy-preloader"></div>
    <% else %>
      <picture>
        <source media="(min-width:1024px)" srcset="<% if function_exists('FocusFill') %>$Image.FocusFill(600,600).URL<% else %>$Image.Fill(600,600).URL<% end_if %>">
        <source media="(max-width:1023px)" srcset="<% if function_exists('FocusFill') %>$Image.FocusFill(600,600).URL<% else %>$Image.Fill(600,600).URL<% end_if %>">
        <img src="<% if function_exists('FocusFill') %>$Image.FocusFill(600,600).URL<% else %>$Image.ScaleMaxWidth(600).URL<% end_if %>"
             alt="$Image.Title.ATT" width="600" height="600"
             style="width:100%;height:auto;">
      </picture>
    <% end_if %>
    <% if $Link %></a><% end_if %>
  <% end_if %>
  <div class="card-section">
    <% if $Title %><h3 class="card-title">$Title</h3><% end_if %>
    
    <% if $StartDate || $EndDate %>
      <p class="promo-dates">
        <% if $StartDate %>$StartDate.Nice<% end_if %>
        <% if $StartDate && $EndDate %> &ndash; <% end_if %>
        <% if $EndDate %>$EndDate.Nice<% end_if %>
      </p>
    <% end_if %>

    <% if $Summary %><p class="card-text">$Summary.LimitCharacters(120)</p><% end_if %>
    <% if $Link %>
      <a class="button small" href="$Link" <% if $OpenInNew %>target="_blank" rel="noopener noreferrer"<% end_if %>>View $Title.XML</a>
    <% end_if %>
  </div>
</div>