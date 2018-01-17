<?php
/**
 * Class CacheCommand
 * Author: Onur CANALP
 *
 * For refresh cache with check active cache status
 *
 * Using: /var/www/onur/project/protected#  php yiic.php cache
 * Optional Params:
 * --force
 * --models=XX,YY,ZZ...
 * --help
 */

class CacheCommand extends CConsoleCommand
{
    //default model names
    private $models = array(
        'XX',
        'YY',
        'ZZ',
    );

    public function actionIndex($force= false, $models= "", $help= "")
    {
        if($help){
            echo "Usage: php yiic.php cache\n";
            echo "Optional Params:\n";
            echo "--force (Force Refresh)\n";
            echo "--models=XX,YY,.. (Refresh wanted models like: XX,YY)\n";
            exit();
        }

        if(!empty($models)){
            $sameModels = array_intersect($this->models, explode(",",$models));
            if(!empty($sameModels))
            $this->models = $sameModels;
        }

        if($force)
            $this->refreshCache();
        else
            $this->check();
    }

    public function check()
    {
        echo "Checking Cache Status...\n";
        $value=Yii::app()->cache->get('memcacheAlreadySet');
        if($value===false)
        {
            $this->refreshCache();
            echo "Cache Refresh Success!\n";
        } else {
            echo "Cache already set!\n";
        }
    }

    public function refreshCache()
    {
        echo "Caching begins! This may take some time depending on the number of models...\n\n";

        foreach ($this->models as $modelName) {
            $model = new $modelName;
            echo "Caching model $modelName (table ".$model->tableName().")...\n";
            Yii::app()->db->schema->getTable($model->tableName(), true);

            echo"Refreshing model $modelName (table ".$model->tableName().")...\n";
            Yii::app()->db->schema->refresh();

            echo "Done\n\n";
        }

        Yii::app()->cache->set('memcacheAlreadySet',time());

        $this->sendMail("<p>These Models Refreshed On Memcache:</p>".implode("<br>",$this->models));
    }

    private function sendMail($message)
    {
        $to = 'onurcanalp@gmail.com';

        $subject = 'Project - Cache';

        $headers = "From: noreply@onurcanalp.com\r\n";
        $headers .= "Reply-To: noreply@onurcanalp.com\r\n";
        //$headers .= "CC: iletisim@onurcanalp.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

        mail($to, $subject, $message, $headers);
    }
}
