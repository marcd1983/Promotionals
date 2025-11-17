<div class="cell">
    <% if $Title && $ShowTitle %>
        <% with $HeadingTag %>
            <{$Me} class="element-title">$Up.Title.XML</{$Me}>
        <% end_with %>
    <% end_if %>
    <% if $Content %><div class="element__content">$Content</div><% end_if %>

    <% if $PromoList %>
    <% if $Appearance = 'Carousel' %>
<<<<<<< HEAD
        <% include Antlion/Promotionals/Includes/PromoCarousel %>
    <% else %>
        <% include Antlion/Promotionals/Includes/PromoGrid %>
=======
        <% include PromoCarousel %>
    <% else %>
        <% include PromoGrid %>
>>>>>>> 10cedb0 (re init)
    <% end_if %>    
    <% end_if %>
</div>