#### Adding tags shouldn't be hard work.

The `li3_taggable` library aims to make it super simple to add tag functionality your models for document databases (i.e. MongoDB).

## Requirements

Tags are stored denomalized with the model in an array.  Therefore, a document database is currently required.  Tags are
also stored in their own collection for use in lists and statistics.

This library integrates with `li3_behaviors` to add the `'taggable'` behavior, but it isn't required that you use `li3_behaviors`.

You can call the `li3_taggable\extensions\data\behavior\Taggable::apply()` method manually inside your model `__init()` instead.

## Adding Tags To Your Model

See the api documentation for `li3_taggable\extensions\data\behavior\Taggable`.  It has all that you need to know.

## Roadmap

Add usage statistics to the `Tags` documents so you can easily sort on the number of times each tag was used.

Might be interesting to index tag statistics by class type that the tags were applied to as well.  This
way you can see all of the tags for your `Posts` models vs other types of models.

We're not in any rush to implement that, so send us a pull request! :)

