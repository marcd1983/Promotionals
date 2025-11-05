

  <div class="swiper" id="carousel-{$ID}" data-element-carousel>
    <div class="swiper-wrapper">
      <% loop $PromoList.Sort(SortOrder) %>
        <div class="swiper-slide">
            <% include Antlion/Promotionals/Includes/PromoCard %>
        </div>
      <% end_loop %>
    </div>
    <% if $Pagination %><div class="swiper-pagination" aria-label="Carousel pagination"></div><% end_if %>
    <% if $Navigation %>
      <button class="swiper-button-prev" aria-label="Previous slide"></button>
      <button class="swiper-button-next" aria-label="Next slide"></button>
    <% end_if %>
    <% if $Scrollbar %><div class="swiper-scrollbar"></div><% end_if %>
  </div>
<script>
document.addEventListener('DOMContentLoaded', function(){
  var el = document.getElementById('carousel-{$ID}');
  if (!el) return;
  var options = {$CarouselOptionsJSON.RAW};
  new Swiper(el, options);
});
</script>
