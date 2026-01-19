<?php
/**
 * Template part for displaying form countries picker
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig;

?>

<div class="form__countries">
    <div class="form__countries__current">
        <div class="form__countries__current__flag">
            <img src="https://cdn.kcak11.com/CountryFlags/countries/us.svg" alt="Current flag">
        </div>
        <span class="form__countries__current__code"></span>
        <span class="form__countries__current__arrow"></span>
    </div>
    <div class="form__countries__list-holder">
        <div class="form__countries__list__search">
            <input type="search" id="countrySearchInput" placeholder="Search countries...">
        </div>
        <div class="form__countries__list"></div>
    </div>
</div>