# Links for Laravel

> 🔗 No more broken internal links!  
> ♻️ Changes made to route structures, model bindings, slugs, etc. are directly transposed to all URLs generated with this package ;  
> 🎯 Display intuitive and exhaustive form fields for links selection ;  
> 💾 Store references to simple or complex URLs where and how you want.

Laravel's routing system is great. With this package, it will become even greater! Using a simple link reference syntax, you'll be able to store and parse URLs on-the-fly and take full advantage of Laravel's routing in rich content and other stored data structures.

Building a content page and want to add the possibility to insert links to your application's resources (or even external!) from your admin panel? You basically have two options:

1. Store the full URL as a string and hope it won't change soon:
    ```
    Hi! What can I get you? We have some great [apples](https://my-application.test/store/food/fruit/organic-apples).
    Love this package? Support us and consider [sponsoring Whitecube](https://github.com/sponsors/whitecube)!
    ```
2. Store a short, immutable reference to this URL and let your application resolve its current, working URL at runtime:
    ```
    Hi! What can I get you? We have some great [apples](#link[products.item@216]).
    Love this package? Support us and consider [sponsoring Whitecube](#link[sponsor])!
    ```

You'll get it: the second one is probably the best. However, it can swiftly become quite complex to setup and maintain. That's why we've built a package for it.

## Table of contents

1. [Installation](#installation)
2. [Usage](#usage)
3. [Editing & storing link references](#editing-storing-link-references)
4. [Resolving link URLs](#resolving-link-urls)
    - [Using the `Link` instantiation methods](#using-the-link-instantiation-methods)
    - [Using the `Links` facade](#using-the-links-facade)
    - [Using the `Str` facade or `str()` helper](#using-the-str-facade-or-str-helper)
    - [Using Blade directives](#using-blade-directives)
    - [Using model attribute casting](#using-model-attribute-casting)
5. [Reporting link resolving issues](#reporting-link-resolving-issues)

## Installation

```bash
composer require whitecube/laravel-links
```

This package will auto-register its service provider.

## Usage

First, you'll need to register the application's link resolvers. Link resolvers are objects used to transform immutable link references (such as `products.item@216`) into fully qualified URLs. Resolvers can handle any of your application's routes or even complex external URLs. This package ships with a few common resolvers but feel free to create your own!

A good place to start is with a Service Provider. You can use an existing one (why not the application's `RouteServiceProvider`?) or create a dedicated Provider (e.g. `LinkServiceProvider`). Then, inside the provider's `boot` method, provide a resolver definition for each link you'll need to reference:

```php
use App\Models\Product;
use Whitecube\Links\Facades\Links;

public function boot()
{
    // Simple routes:
    Links::route('home');
    Links::route('about')->title('About us');
    Links::route(name: 'catalog', parameters: ['list' => 'bestselling'])->title('Popular products');

    // Group resources as an "archive":
    Links::archive('products')
        ->index(fn($link) => $link->route('products')->title('All products'))
        ->items(fn($link) => $link->route('product')
            ->model(Product::class)
            ->title(fn($product) => $product->name)
        );
}
```

You can now start [inserting link references](#editing-storing-link-references) and [resolving](#resolving-link-urls) them for display.

## Editing & storing link references

WIP.

## Resolving link URLs

As stated above, links can be stored in many ways, depending on your use case. Here are a few common methods that should get you started.

### Using the `Link` instantiation methods

WIP

### Using the `Links` facade

WIP

### Using the `Str` facade or `str()` helper

WIP

### Using Blade directives

WIP

### Using model attribute casting

Beware that model attribute casting can have undesired side-effects on dedicated link editor components since they'll probably rely on the attribute's raw value (meaning "with unresolved link references") to work.

#### Casting textual content with inline link references (inline tags)

The `ResolvedInlineLinkTagsString` cast is useful when a model has attributes containing editorial content with inline link references (also called "inline tags") that all need to be resolved at once.

```php
use Illuminate\Database\Eloquent\Model;
use Whitecube\Links\Casts\ResolvedInlineLinkTagsString;

class Post extends Model
{
    protected $casts = [
        'content' => ResolvedInlineLinkTagsString::class,
    ];
}
```

## Reporting link resolving issues

WIP.

---

## Development Roadmap

Links are at the base of the world wide web. This package aims to "objectify" and take inventory of a project's internal URLs, which opens a lot of possibilites for future features and updates.

- [x] List available link resolvers ;
- [x] Generate URLs from link references/objects ;
- [x] Generate full list of available concrete links for selectors & specialized form UI components ;
- [ ] Replace inline link references with their resolved URL value ;
- [ ] Generate URLs with `@link()` blade directive ;
- [ ] Define link resolvers directly on Laravel's routes ;
- [ ] Cast Link objects in models ;
- [ ] Report unresolvable link references ;
- [ ] Generate XML sitemaps based on the links inventory ;
- [ ] Reverse-engineer URLs to detailed Link objects ;

Need some of the unchecked features above? Or want to add items to this list? Open an issue or a PR and we'll take a look at it! But remember, if you want professional support, don't forget to sponsor us! 🤗

## 🔥 Sponsorships 

If you are reliant on this package in your production applications, consider [sponsoring us](https://github.com/sponsors/whitecube)! It is the best way to help us keep doing what we love to do: making great open source software.

## Contributing

Feel free to suggest changes, ask for new features or fix bugs yourself. We're sure there are still a lot of improvements that could be made, and we would be very happy to merge useful pull requests. Thanks!

## Made with ❤️ for open source

At [Whitecube](https://www.whitecube.be) we use a lot of open source software as part of our daily work.
So when we have an opportunity to give something back, we're super excited!

We hope you will enjoy this small contribution from us and would love to [hear from you](mailto:hello@whitecube.be) if you find it useful in your projects. Follow us on [Twitter](https://twitter.com/whitecube_be) for more updates!
