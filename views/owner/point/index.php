<?php

use app\services\Address\dto\AddressDto;
use yii\helpers\Html;

/** @var AddressDto[] $addresses */
/** @var int|null $addressId */

$this->title = 'Список добавленных сегодня кодов';
?>
<section>
    <h2>Список добавленных сегодня кодов</h2>

    <form method="get" class="mb-3">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-6">
                <label class="form-label" for="addressFilter">Фильтр по адресу</label>
                <select id="addressFilter" class="form-select" name="addressId">
                    <option value="">Все адреса</option>
                    <?php foreach ($addresses as $address): ?>
                        <option value="<?= $address->id ?>" <?= $addressId === $address->id ? 'selected' : '' ?>>
                            <?= Html::encode(($address->companyName ?? 'Компания') . ' — ' . $address->address) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-primary w-100">Показать</button>
            </div>
        </div>
    </form>

    <?= $grid ?>
</section>
