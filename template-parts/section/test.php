<?php

/**
 * Template part for displaying the test section
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;


$imagesPNG = ['1.png', '2.png', '3.png', '4.png', '5.png', '6.png', '7.png', '8.png', '9.png', '10.png', '11.png', '12.png', '13.png', '14.png', '15.png', '16.png', '17.png', '18.png', '19.png', '20.png', '21.png', '22.png', '23.png', '24.png', '25.png', '26.png', '27.png', '28.png', '29.png', '30.png',  '31.png', '32.png', '33.png', '34.png', '35.png', '36.png', '37.png', '38.png', '39.png', '40.png', '41.png', '42.png', '43.png'];
$imagesSVG = ['1.svg', '2.svg', '3.svg', '4.svg', '5.svg', '6.svg', '7.svg', '8.svg', '9.svg', '10.svg', '11.svg', '12.svg', '13.svg', '14.svg', '15.svg', '16.svg', '17.svg', '18.svg', '19.svg', '20.svg', '21.svg', '22.svg', '23.svg', '24.svg', '25.svg', '26.svg', '27.svg', '28.svg', '29.svg', '30.svg', '31.svg', '32.svg', '33.svg', '34.svg', '35.svg', '36.svg', '37.svg', '38.svg', '39.svg', '40.svg', '41.svg', '42.svg', '43.svg', '44.svg', '45.svg', '46.svg', '47.svg', '48.svg', '49.svg', '50.svg', '51.svg', '52.svg', '53.svg', '54.svg'];
$imagesSVGInline = ['1.svg', '2.svg', '3.svg', '4.svg', '5.svg', '6.svg', '7.svg', '8.svg', '9.svg', '10.svg', '11.svg', '12.svg', '13.svg', '14.svg', '15.svg', '16.svg', '17.svg', '18.svg', '19.svg', '20.svg', '21.svg', '22.svg', '23.svg', '24.svg', '25.svg', '26.svg', '27.svg', '28.svg', '29.svg', '30.svg', '31.svg', '32.svg', '33.svg', '34.svg', '35.svg', '36.svg', '37.svg', '38.svg', '39.svg', '40.svg', '41.svg', '42.svg', '43.svg', '44.svg', '45.svg', '46.svg', '47.svg', '48.svg', '49.svg', '50.svg', '51.svg', '52.svg', '53.svg', '54.svg', 'test.svg'];

?>


<section class="is-flex flex-direction--column">
  <h2 class="my-m">Images</h2>
  <section class="test-section">
    <?php foreach ($imagesPNG as $image) : ?>
      <div class="test-section__image-wrapper">
        <img class="test-section__image" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/test_copy/<?php echo $image; ?>" alt="<?php echo $image; ?>">
      </div>
    <?php endforeach; ?>
  </section>
  <h2 class="my-m">SVG in image</h2>
  <section class="test-section">
    <?php foreach ($imagesSVG as $image) : ?>
      <div class="test-section__image-wrapper">
        <img class="test-section__image" src="<?php echo get_stylesheet_directory_uri() ?>/assets/images/test_copy/<?php echo $image; ?>" alt="<?php echo $image; ?>">
      </div>
    <?php endforeach; ?>
  </section>
  <h2 class="my-m">inline SVG</h2>
  <section class="test-section">
    <?php foreach ($imagesSVGInline as $image) : ?>
      <div class="test-section__image-wrapper">
        <?php echo \get_svg_icon(basename($image, '.svg'), array('class' => 'test-section__image')); ?>
      </div>
    <?php endforeach; ?>
    <div class="test-section__image-wrapper">
      <?php echo \get_svg_icon('test', array('class' => 'test-section__image-1', 'width' => '24', 'height' => '24')); ?>
    </div>
  </section>
</section>