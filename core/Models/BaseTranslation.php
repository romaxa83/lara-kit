<?php

namespace Core\Models;

use App\Traits\ModelTranslation;

/**
 * @property int id
 * @property string lang
 * @property int row_id
 */
abstract class BaseTranslation extends BaseModel
{
    use ModelTranslation;
}
