<div class="main grid-container $PageWidth" role="main">
<% if $Promo %>
<div class="promo-detail">
  <nav class="grid-x grid-padding-x grid-padding-y align-right">
    <div class="cell shrink">
        <a class="button hollow tiny" href="$Top.Link">&larr; Back to $Top.Title.XML</a>
    </div>
  </nav>
  <div class="grid-x grid-padding-x grid-padding-y">
    <div class="cell auto">
      <% if $Promo.Image %>
        <figure class="promo-detail__figure">
          <img
            src="$Promo.Image.FocusFill(800,800).URL"
            alt="$Promo.Image.Title.ATT"
            loading="lazy" />
        </figure>
      <% end_if %>
    </div>

    <div class="cell large-7">
      <header class="promo-header">
        <h1 class="promo-title">$Promo.Title.XML</h1>
        <%-- Optional date window if your Promo has StartDate/EndDate --%>
        <% if $Promo.StartDate || $Promo.EndDate %>
          <p class="promo-dates">
            <% if $Promo.StartDate %>$Promo.StartDate.Nice<% end_if %>
            <% if $Promo.StartDate && $Promo.EndDate %> &ndash; <% end_if %>
            <% if $Promo.EndDate %>$Promo.EndDate.Nice<% end_if %>
          </p>
        <% end_if %>
      </header>

      <% if $Promo.Summary %>
        <div class="promo-summary">
          <p>$Promo.Summary</p>
        </div>
      <% end_if %>

      <%-- Optional CTA buttons (if using LinkField / MultiLinkField as $Promo.Links) --%>
      <% if $Promo.Links.Exists %>
          <div class="button-group <% if $Align == 'center' %>align-center<% else_if $Align == 'right' %>align-right<% else %>align-left<% end_if %>">
            <% loop $Promo.Links %>
             <a class="button $CssClass" href="$URL" <% if $OpenInNew %>target="_blank" rel="noopener noreferrer"<% end_if %>>$Title.XML</a>
            <% end_loop %>
          </div>
        <% end_if %>
    </div>
  </div>


  <div class="grid-x grid-padding-x grid-padding-y">
    <div class="cell">
      <div class="promo-content">
        <%-- $Promo.Content --%>
        $Promo.ElementalArea
      </div>
    </div>
  </div>

  <div class="grid-x grid-padding-x grid-padding-y">
    <div class="cell">
      <div class="promo-enquiry">
        <h3>Ask about this promotion</h3>
          <% include FormMessageToast %>
          $PromoForm
      </div>
    </div>
  </div>

</div>
<% else %>
  <%-- Fallback (usually not hit because controller 404s when missing) --%>
  <div class="grid-x grid-padding-x grid-padding-y">
    <div class="cell">

      <div class="callout text-center">

      <div class="toast toast--success toast--auto-hide callout alert p-40 text-center">

         <p>Sorry, we couldn’t find that promotion.</p>
        <a class="button hollow" href="$Top.Link">&larr; Back to $Top.Title.XML</a>
      </div>
    </div>
  </div>
<% end_if %>
</div>