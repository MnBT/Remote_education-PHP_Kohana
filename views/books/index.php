<?php

echo View::factory('books/list', array('model' => $model, 'pagination' => $pagination));
