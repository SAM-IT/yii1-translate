<?php

    namespace SamIT\Yii1\Models;
    use \Yii;
    /**
     * @property string $model
     * @propert string $model_id
     * @property string $language
     */
    class Translation extends ActiveRecord 
    {
        protected static $tableName = 'befound_translation';
        
        public $dataStore = [];
        
        public function __construct($scenario = 'insert') {
            parent::__construct($scenario);
        }
        
        public function __get($name) {
			try {
				return parent::__get($name);
			} catch (\Exception $ex) {
				$e = $ex;
			}

			if (array_key_exists($name, $this->dataStore)) {
				return $this->dataStore[$name];
			}
			throw $e;
		}

		public function __isset($name)
		{
			return parent::__isset($name) || isset($this->dataStore[$name]);
		}
		
		public function __set($name, $value) {
			$e;
			try {
				return parent::__set($name, $value);
			} catch (\Exception $ex) {
				$e = $ex;
			}

			$this->dataStore[$name] = $value;
		}
        
        public function behaviors() {
            return array_merge(parent::behaviors(), [
                JsonBehavior::CLASS => [
					'class' => JsonBehavior::CLASS,
					'jsonAttributes' => ['dataStore' => 'data']
				]
            ]);
        }
        public static function createTable() {
            if (in_array(static::$tableName, \Yii::app()->db->schema->tableNames)) {
                \Yii::app()->db->createCommand()->dropTable(static::$tableName);
            }
            $columns = [
                'model_id' => 'int NOT NULL',
                'model' => 'string(100) CHARACTER SET ascii NOT NULL',
                'language' => 'string(10) CHARACTER SET ascii NOT NULL',
                'data' => 'binary NOT NULL',
            ];
            \Yii::app()->db->createCommand()->createTable(self::$tableName, $columns);
            \Yii::app()->db->createCommand()->addPrimaryKey('PRIMARY', self::$tableName, [
                'model_id',
                'model',
                'language'
            ]);
        }
        
        public function relations() {
            return [
                'owner' => [self::BELONGS_TO, \CActiveRecord::class, 'model_id']
            ];
        }
        public function tableName() {
            return static::$tableName;
        }
        
    }