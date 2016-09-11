# Render Cache Example
This module is the code for the [session](http://glsr.it/DrupalGRXSun) about caching in DrupalCamp Spain 2016 (Granada).

It contains an example of the practical use of cache tags and cache contexts, along with common pitfalls and how to solve them.

## What does it do?
This module provides a block that you can place in any region of your choosing. Once placed, the block will output the title of the first node ever created and a greeting to the current logged in user.

The different branches in the module show de development process of this simple task, and how important is to have caches in mind when you are rendering any thing in the page.

### Stages

#### 1. Added the title without cache tags
At this point we have created a block that retrieves the node we are looking for and puts the title in the page. We can see at this point that after a couple of refreshes the content of the block is cached. The problem arises when we edit the title of the node, the content of the block does not update.
##### Resources
  - [Code at this point](https://github.com/e0ipso/render_cache_example/tree/1-cache-tags-error).
  - [Video](https://drive.google.com/open?id=0B_nOUMcmnVmCcHdNbW1TZ0QyMFU).

#### 2. Fixed adding the cache tags
At this point we realize that we need to invalidate the cache when the node is updated. For that we need to use cache tags.

The recommended way to add cacheability metadata (includes cache tags) to a render array is to use `addCacheabilityDependency`. Doing that will have the effect of adding (the number `1` may vary depending on the calculated node):
```php
  $build['#cache']['tags'] = ['node:1'];
```

With this technique we have fixed our problem with _stale caches_.
##### Resources
  - [Code at this point](https://github.com/e0ipso/render_cache_example/tree/2-cache-tags-fixed).
  - [Diff with previous state](https://github.com/e0ipso/render_cache_example/compare/1-cache-tags-error...2-cache-tags-fixed?diff=unified&expand=1&name=2-cache-tags-fixed).
  - [Video](https://drive.google.com/open?id=0B_nOUMcmnVmCLXh2c1BwaGdkbnc).

#### 3. Added the greeting without cache contexts
At this point we want to add a greeting to the logged in user. Once that is done, we realize that two different authenticated users get the **same** cached results. That is not good.

##### Resources
  - [Code at this point](https://github.com/e0ipso/render_cache_example/tree/3-cache-context-error).
  - [Diff with previous state](https://github.com/e0ipso/render_cache_example/compare/2-cache-tags-fixed...3-cache-context-error?diff=unified&expand=1&name=3-cache-context-error).
  - [Video](https://drive.google.com/open?id=0B_nOUMcmnVmCeGstQkJualpxV1E).

#### 4. Fixed adding the cache contexts
We realize that we need to have different versions of the cache for the _username_ render array depending on the user viewing the content. That is exactly what cache contexts are for. We add the cache context for the current user (in this case we add them manually for the sake of the example, although we should be following the best practices and use `addCacheabilityDependency`).

Once that is done we don't have _cache poisoning_ anymore.
##### Resources
  - [Code at this point](https://github.com/e0ipso/render_cache_example/tree/4-cache-context-fixed).
  - [Diff with previous state](https://github.com/e0ipso/render_cache_example/compare/3-cache-context-error...4-cache-context-fixed?diff=unified&expand=1&name=4-cache-context-fixed).
  - [Video](https://drive.google.com/open?id=0B_nOUMcmnVmCN0hhUUkxUF9pek0).

## Disclaimer
This module does not provide any useful functionality, but aims to show the Render Cache subsystem's capabilities.
