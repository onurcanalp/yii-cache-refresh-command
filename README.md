
# yii-cache-refresh-command
Yii Framework has some problems with Oracle when tries to make a schema cache.. Lets fix it...

If You Want to refresh default all models in project, you can modify **refreshCache()** method like this:
```
// Load all tables of the application in the schema
echo "Begin\n";
Yii::app()->db->schema->getTables();
echo "Now refresh()\n";
// clear the cache of all loaded tables
Yii::app()->db->schema->refresh();
echo "End\n";
```
