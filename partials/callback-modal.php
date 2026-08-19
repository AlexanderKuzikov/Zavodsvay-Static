<?php
/**
 * callback-modal.php — модальное окно «Заказать звонок»
 * Отрисовывается на всех страницах, логика в assets/js/template.js
 */
?>
<div class="callback-modal" id="callback-modal" hidden>
    <div class="callback-modal__overlay" data-callback-close></div>
    <div class="callback-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="callback-modal-title" aria-describedby="callback-modal-desc">
        <button type="button" class="callback-modal__close" data-callback-close aria-label="Закрыть">&times;</button>
        <h2 class="callback-modal__title" id="callback-modal-title">Заказать звонок</h2>
        <p class="callback-modal__desc" id="callback-modal-desc">Оставьте номер телефона — перезвоним в рабочее время (пн–пт 9:00–18:00).</p>
        <form class="callback-modal__form" id="callback-form" novalidate>
            <label class="callback-modal__label" for="callback-phone">Ваш телефон</label>
            <input class="callback-modal__input" type="tel" name="phone" id="callback-phone"
                   placeholder="+7 (___) ___-__-__" autocomplete="tel"
                   inputmode="tel" maxlength="25" required>
            <span class="callback-modal__error" id="callback-error" hidden></span>
            <input type="text" name="company" class="callback-hp" tabindex="-1" autocomplete="off" aria-hidden="true">
            <button type="submit" class="callback-modal__submit">Жду звонка</button>
        </form>
        <div class="callback-modal__status" id="callback-status" hidden></div>
    </div>
</div>
