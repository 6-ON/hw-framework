<?php

namespace sixon\hwFramework;

use sixon\hwFramework\db\DbModel;

abstract class UserModel extends DbModel
{

    abstract public function getDisplayName(): string;
    abstract public function getDisplayEmail(): string;
}
