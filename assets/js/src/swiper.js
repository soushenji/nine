/**
 * 幻灯片
 */
export default function () {
  // 幻灯
  let swiperMain = new Swiper('.swiper-container', {
    pagination: {
      el: '.swiper-pagination',
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    }
  });
}