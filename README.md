# CakePHP Closure Behavior
An efficient alternative to the CakePHP Tree Behavior

## Background
I was working on a project, where I listed multiple posts on the homepage and wanted to show the categories they fell under as breadcrumbs. Normally the tree behavior works just fine, but if I have 40 posts, I now do 1 query for the list of posts and 40 queries to get a list of all ancestor categories for the post. This didn't seem like a good idea, so I started to look for ways around this. generateTreeList and an iterator function seemed more viable, cutting my query down to 2, but the function itself would prove to be complex and slow.

After searching for feedback on irc.freenode.net/php someone pointed me to an [interesting slideshow](http://www.slideshare.net/billkarwin/sql-antipatterns-strike-back) where I learned about the 'Closure' pattern (page 68). It seems to be a much more efficient alternative to MPTT. So this is my attempt to implement it.

An interesting example implementation: http://codepad.org/pR5r68V0

## Installation
### With Git

```
git submodule add git@github.com:ProLoser/CakePHP-ClosureBehavior.git Plugin/Closure
```

### Without Git
[Download package](https://github.com/ProLoser/CakePHP-ClosureBehavior/downloads) and extract to Plugin/Closure
