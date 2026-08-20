<?php
/** Reusable signup form. */
$directions = p313_option( 'form_directions', array() );
if ( ! is_array( $directions ) || ! $directions ) {
	$directions = array(
		array( 'label' => 'Jazz-modern' ),
		array( 'label' => 'Modern' ),
		array( 'label' => 'Классическая хореография' ),
		array( 'label' => 'Актёрское мастерство' ),
		array( 'label' => 'Растяжка / Stretching' ),
		array( 'label' => 'Детские группы' ),
	);
}
$success_title = p313_option( 'form_success_title', 'Заявка принята' );
$success_text  = p313_option( 'form_success_text', 'Спасибо! Мы позвоним вам в течение дня и подберём удобное время для просмотра.' );
$submit_label  = p313_option( 'form_submit_label', 'Записаться на просмотр' );
$call_label    = p313_option( 'form_call_label', 'Позвонить' );
$form_note     = p313_option( 'form_note', 'Нажимая кнопку, вы соглашаетесь с политикой конфиденциальности' );
$phone         = p313_phone();
$phone_href    = p313_phone_href();
?>
<form class="form <?php echo esc_attr( $args['class'] ?? '' ); ?>" data-signup-form novalidate>
 <div class="form__success panel-hidden" data-form-success><div class="form__success-icon"><?php echo p313_icon( 'plus' ); ?></div><h3 class="form__success-title"><?php echo esc_html( $success_title ); ?></h3><p class="form__success-text" data-form-success-text><?php echo esc_html( $success_text ); ?></p><button class="btn btn--secondary panel-hidden" type="button" data-form-close>Закрыть</button></div>
 <div data-form-fields><div class="form__row form__row--compact"><div class="form__field"><label class="form__label" for="p313-name">Имя</label><input class="form__input" id="p313-name" type="text" name="name" placeholder="Как к вам обращаться" data-field-name></div><div class="form__field"><label class="form__label" for="p313-phone">Телефон <span>*</span></label><input class="form__input" id="p313-phone" type="tel" name="phone" placeholder="+7 ___ ___ __ __" inputmode="tel" data-field-phone required><p class="form__error panel-hidden" data-phone-error>Укажите корректный номер телефона</p></div></div>
 <div class="form__field"><label class="form__label" for="p313-direction">Направление</label><div class="form__select-wrap"><select class="form__select" id="p313-direction" name="direction" data-field-direction><option value="">Не выбрано — поможем определиться</option><?php foreach ( $directions as $direction ) : $value = p313_row_label( $direction ); if ( $value ) : ?><option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $value ); ?></option><?php endif; endforeach; ?></select><span class="form__select-icon"><?php echo p313_icon( 'arrow' ); ?></span></div></div>
 <div class="form__field"><label class="form__label" for="p313-comment">Комментарий</label><textarea class="form__textarea" id="p313-comment" name="comment" rows="3" placeholder="Возраст, пожелания, вопросы"></textarea></div>
 <div class="form__actions">
  <button class="btn btn--primary btn--full" type="submit" data-form-submit><?php echo esc_html( $submit_label ); ?></button>
  <a class="btn btn--secondary btn--full form__call" href="<?php echo esc_url( $phone_href ); ?>"><?php echo esc_html( $call_label ); ?> · <?php echo esc_html( $phone ); ?></a>
 </div>
 <p class="form__note"><?php echo esc_html( $form_note ); ?></p></div>
</form>
