<?php
/**
 * Template part for displaying the Exampleform
 *
 * @package wp_rig
 */

namespace WP_Rig\WP_Rig; ?>

<form id="example-form" class="form floating-label">

    <div class="form__row">
        <div class="form__field">
            <label for="form-name">Name<span class="color--red">*</span></label>
            <input type="text" id="form-name" name="name" placeholder="Maria" required>
        </div>
    </div>

    <div class="form__row">
        <div class="form__field">
            <label for="form-email">E-mail <span class="color--red">*</span></label>
            <input type="email" id="form-email" name="email" placeholder="example@example.com" required>
        </div>
        
        <div class="form__field form__field--tel">
            <label for="form-phone">Phone Number <span class="color--red">*</span></label>
            <div class="is-flex w-100">
                <?php get_template_part(
                	'template-parts/forms/countries-dropdown',
                ); ?>
                <input type="tel" id="form-phone" name="phone" placeholder="+381 64 123 4567" required>
            </div>
        </div>
    </div>

    <div class="form__row">
        <div class="form__field form__field--select">
            <label for="form-name">Select<span class="color--red">*</span></label>
            <select id="form-select" name="select" required>
                <option value="1">Option 1</option>
                <option value="2">Option 2</option>
                <option value="3">Option 3</option>
            </select>
        </div>
    </div>

    <div class="form__row form__row--submit">
        <div class="form__field form__field--submit">
            <button type="submit" class="btn btn--primary">Send</button>
        </div>
    </div>
</form>