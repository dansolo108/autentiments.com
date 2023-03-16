{extends 'template:1'}
{block 'main'}
    <main class="au-error  container">
      <h1 class="au-h1  au-error__title">{$_modx->resource.pagetitle}</h1>
      <p class="au-error__text">{'stik_404_text_1' | lexicon}</p>
      <p class="au-error__text">{'stik_404_text_2' | lexicon}</p>

      <section class="au-home__categories">
        <a class="au-home__category  au-scroll-animat  category-col" href="{7|url}">
          <div class="au-home__category-img-box">
            <picture>
              <!-- <source media="(min-width: 1024px)" type="image/webp" srcset=""> -->
              <!-- <source type="image/webp" srcset=""> -->
              <source media="(min-width: 1024px)" srcset="/assets/tpl/img/au-home__category-img-1_desktop.jpg">    <!-- width="614" height="732" -->
              <img width="335" height="400" class="au-home__category-img" src="/img/au-home__category-img-1_mobile.jpg" alt="">
            </picture>
          </div>
          <h2 class="au-home__category-title">Bestsellers</h2>
        </a>
        <a class="au-home__category  au-scroll-animat  category-col" href="{23|url}">
          <div class="au-home__category-img-box">
            <picture>
              <!-- <source media="(min-width: 1024px)" type="image/webp" srcset=""> -->
              <!-- <source type="image/webp" srcset=""> -->
              <source media="(min-width: 1024px)" srcset="/assets/tpl/img/au-home__category-img-2_desktop.jpg">    <!-- width="614" height="732" -->
              <img width="335" height="400" class="au-home__category-img" src="/img/au-home__category-img-2_mobile.jpg" alt="">
            </picture>
          </div>
          <h2 class="au-home__category-title">Sale</h2>
        </a>
      </section>
    </main>
{/block}