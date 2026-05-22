<?php
declare(strict_types=1);

/** Публичные ссылки на соцсети и мессенджеры */
const LINKEDIN_PROFILE_URL = 'https://www.linkedin.com/in/elina-moshkovich-41397115';
const TELEGRAM_CHANNEL_URL = 'https://t.me/ellin_mos';
const TELEGRAM_MEMBERSHIP_URL = 'https://t.me/ellin_mos';
const CALENDLY_BOOKING_URL = 'https://calendly.com/elina-moshkovich/30min';

/** Режим статической сборки: задаётся в init.sh (SITE_STATIC_EXPORT=1, SITE_LANG=en|ru) */
$staticExport = getenv('SITE_STATIC_EXPORT') === '1';

$locale = 'en';
if ($staticExport) {
    $env = getenv('SITE_LANG') ?: 'en';
    $locale = in_array($env, ['en', 'ru'], true) ? $env : 'en';
} elseif (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'ru'], true)) {
    $locale = $_GET['lang'];
}

$langFile = __DIR__ . '/lang/' . $locale . '.php';
if (! is_file($langFile)) {
    $locale = 'en';
    $langFile = __DIR__ . '/lang/en.php';
}

/** @var array<string, string> $translations */
$translations = require $langFile;

function __(string $key): string
{
    global $translations;

    return $translations[$key] ?? $key;
}

function seo_plain(string $key): string
{
    return trim(html_entity_decode(strip_tags(__($key)), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
}

/** Базовый URL без завершающего слэша. При необходимости переопределите переменной окружения SITE_BASE_URL. */
$siteBaseUrl = rtrim((string) (getenv('SITE_BASE_URL') ?: 'https://riskuni.com'), '/');
$canonicalPath = $staticExport
    ? ($locale === 'ru' ? '/ru.html' : '/')
    : '/index.php' . ($locale === 'ru' ? '?lang=ru' : '');
$canonicalUrl = $siteBaseUrl . $canonicalPath;
$sitemapUrl = $siteBaseUrl . '/sitemap.xml';
$ogImageUrl = $siteBaseUrl . '/img/hero.jpeg';
$ogLocale = $locale === 'ru' ? 'ru_RU' : 'en_US';

$personId = $canonicalUrl . '#person-elina';
$businessId = $canonicalUrl . '#risk-university';

$ldGraph = [
    [
        '@type' => 'Person',
        '@id' => $personId,
        'name' => seo_plain('person-name'),
        'givenName' => seo_plain('person-given-name'),
        'familyName' => seo_plain('person-family-name'),
        'jobTitle' => 'Risk Consultant',
        'description' => seo_plain('seo-description'),
        'url' => $canonicalUrl,
        'sameAs' => [LINKEDIN_PROFILE_URL],
        'knowsAbout' => [
            'Enterprise risk management',
            'Corporate governance',
            'COSO ERM',
            'ISO 31000',
            'Fractional CRO',
        ],
        'areaServed' => [
            ['@type' => 'Place', 'name' => 'Europe'],
            ['@type' => 'Place', 'name' => 'United States'],
            ['@type' => 'Place', 'name' => 'GCC'],
        ],
        'worksFor' => ['@id' => $businessId],
    ],
    [
        '@type' => 'ProfessionalService',
        '@id' => $businessId,
        'name' => 'Risk University',
        'description' => seo_plain('seo-description'),
        'url' => $canonicalUrl,
        'image' => $ogImageUrl,
        'areaServed' => [
            ['@type' => 'Place', 'name' => 'Europe'],
            ['@type' => 'Place', 'name' => 'United States'],
            ['@type' => 'Place', 'name' => 'GCC'],
        ],
        'founder' => ['@id' => $personId],
        'sameAs' => [LINKEDIN_PROFILE_URL, TELEGRAM_CHANNEL_URL],
    ],
    [
        '@type' => 'Service',
        '@id' => $canonicalUrl . '#service-risk-system-design',
        'name' => seo_plain('ec-1-t'),
        'description' => seo_plain('ec-1-d'),
        'url' => $canonicalUrl . '#expertise',
        'provider' => ['@id' => $businessId],
        'areaServed' => ['Europe', 'United States', 'GCC'],
    ],
    [
        '@type' => 'Service',
        '@id' => $canonicalUrl . '#service-investor-readiness',
        'name' => seo_plain('ec-2-t'),
        'description' => seo_plain('ec-2-d'),
        'url' => $canonicalUrl . '#expertise',
        'provider' => ['@id' => $businessId],
        'areaServed' => ['Europe', 'United States', 'GCC'],
    ],
    [
        '@type' => 'Service',
        '@id' => $canonicalUrl . '#service-fractional-cro',
        'name' => seo_plain('ec-6-t'),
        'description' => seo_plain('ec-6-d'),
        'url' => $canonicalUrl . '#expertise',
        'provider' => ['@id' => $businessId],
        'areaServed' => ['Europe', 'United States', 'GCC'],
    ],
    [
        '@type' => 'FAQPage',
        'mainEntity' => array_map(
            static function (int $i): array {
                return [
                    '@type' => 'Question',
                    'name' => seo_plain('faq-' . $i . '-q'),
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => seo_plain('faq-' . $i . '-a'),
                    ],
                ];
            },
            [1, 2, 3, 4]
        ),
    ],
];

$structuredDataJson = json_encode(
    [
        '@context' => 'https://schema.org',
        '@graph' => $ldGraph,
    ],
    JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_APOS
);

?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($locale, ENT_QUOTES, 'UTF-8'); ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?php echo htmlspecialchars(__('seo-title'), ENT_QUOTES, 'UTF-8'); ?></title>
<meta name="description" content="<?php echo htmlspecialchars(__('seo-description'), ENT_QUOTES, 'UTF-8'); ?>">
<meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
<link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
<link rel="sitemap" type="application/xml" title="Sitemap" href="<?php echo htmlspecialchars($sitemapUrl, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="google-site-verification" content="7subHqvKilqm_hr2FQHkFqMGF0v_CtLnQRi4yAvc4Oc">
<meta property="og:type" content="website">
<meta property="og:locale" content="<?php echo htmlspecialchars($ogLocale, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:title" content="<?php echo htmlspecialchars(__('seo-title'), ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:description" content="<?php echo htmlspecialchars(__('seo-description'), ENT_QUOTES, 'UTF-8'); ?>">
<meta property="og:image" content="<?php echo htmlspecialchars($ogImageUrl, ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?php echo htmlspecialchars(__('seo-title'), ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:description" content="<?php echo htmlspecialchars(__('seo-description'), ENT_QUOTES, 'UTF-8'); ?>">
<meta name="twitter:image" content="<?php echo htmlspecialchars($ogImageUrl, ENT_QUOTES, 'UTF-8'); ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="preload" as="image" href="img/hero.jpeg" fetchpriority="high">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;family=Source+Serif+4:opsz,wght@8..60,400;8..60,500;8..60,600;8..60,700&amp;display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
<script type="application/ld+json"><?php echo $structuredDataJson; ?></script>
</head>
<body>

<!-- MOBILE MENU -->
<div class="mob-menu" id="mobMenu">
  <button class="mob-close" id="mobClose">&#x2715;</button>
  <a href="#triggers" onclick="closeMob()"><?php echo __('nav-1'); ?></a>
  <a href="#about" onclick="closeMob()"><?php echo __('nav-2'); ?></a>
  <a href="#expertise" onclick="closeMob()"><?php echo __('nav-3'); ?></a>
  <a href="#proof" onclick="closeMob()"><?php echo __('nav-4'); ?></a>
  <a href="#insights" onclick="closeMob()"><?php echo __('nav-5'); ?></a>
  <a href="#media" onclick="closeMob()"><?php echo __('eb-media'); ?></a>
  <a href="#community" onclick="closeMob()"><?php echo __('nav-6'); ?></a>
  <a href="<?php echo htmlspecialchars(CALENDLY_BOOKING_URL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" onclick="closeMob()" style="color:var(--accent);"><?php echo __('nav-cta'); ?></a>
</div>

<!-- NAV -->
<nav>
  <div class="nav-inner">
    <a href="#top" class="nb">
      <div class="nb-mark">R</div>
      <div class="nb-text">
        <span class="nb-name">Risk University</span>
        <span class="nb-sub">Europe &middot; USA &middot; GCC</span>
      </div>
    </a>
    <ul class="nl">
      <li><a href="#triggers"><?php echo __('nav-1'); ?></a></li>
      <li><a href="#about"><?php echo __('nav-2'); ?></a></li>
      <li><a href="#expertise"><?php echo __('nav-3'); ?></a></li>
      <li><a href="#proof"><?php echo __('nav-4'); ?></a></li>
      <li><a href="#insights"><?php echo __('nav-5'); ?></a></li>
      <li><a href="#media"><?php echo __('nav-media'); ?></a></li>
      <li><a href="#community"><?php echo __('nav-6'); ?></a></li>
    </ul>
    <div class="lang-toggle">
<?php if ($staticExport) { ?>
      <a class="lang-btn<?php echo $locale === 'en' ? ' active' : ''; ?>" href="/">EN</a>
      <span class="lang-sep">/</span>
      <a class="lang-btn<?php echo $locale === 'ru' ? ' active' : ''; ?>" href="/ru.html">RU</a>
<?php } else { ?>
      <a class="lang-btn<?php echo $locale === 'en' ? ' active' : ''; ?>" href="?lang=en">EN</a>
      <span class="lang-sep">/</span>
      <a class="lang-btn<?php echo $locale === 'ru' ? ' active' : ''; ?>" href="?lang=ru">RU</a>
<?php } ?>
    </div>
    <a href="<?php echo htmlspecialchars(CALENDLY_BOOKING_URL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="nc"><?php echo __('nav-cta'); ?></a>
    <button type="button" class="ham" onclick="toggleMob()"><span></span><span></span><span></span></button>
  </div>
</nav>

<!-- HERO -->
<section class="hero" id="top">
  <div class="hero-inner">
    <div class="hero-text">
      
      <div class="hero-eyebrow"><?php echo __('hero-eyebrow'); ?></div>
      <h1 class="--<?php echo $locale ?>"><?php echo __('hero-h1'); ?></h1>
      <p class="hero-lead"><?php echo __('hero-lead'); ?></p>
      <div class="hero-cta">
        <a href="<?php echo htmlspecialchars(CALENDLY_BOOKING_URL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="btn-primary"><?php echo __('hero-cta-1'); ?></a>
        <a href="#triggers" class="btn-text"><?php echo __('hero-cta-2'); ?></a>
      </div>
      <div class="hero-stats">
        <div><div class="hst-n">15<span class="small">+</span></div><div class="hst-l"><?php echo __('hero-st-1'); ?></div></div>
        <div><div class="hst-n">8<span class="small">+</span></div><div class="hst-l"><?php echo __('hero-st-2'); ?></div></div>
        <div><div class="hst-n" style="font-size:1.4rem; line-height:1.3; letter-spacing:.02em;">MetLife<br>Allianz<br>PwC · KPMG</div><div class="hst-l"><?php echo __('hero-st-3'); ?></div></div>
      </div>
    </div>
    <div class="hero-photo">
      <img src="img/hero.jpeg" alt="<?php echo htmlspecialchars(__('img-alt-hero'), ENT_QUOTES, 'UTF-8'); ?>" fetchpriority="high" decoding="async">
      <div class="hero-photo-card">
        <div class="hpc-name"><?php echo __('person-name'); ?></div>
        <div class="hpc-role"><?php echo __('hpc-role'); ?></div>
        <span class="hpc-line"></span>
        <div class="hpc-cred"><?php echo __('hpc-cred'); ?></div>
        <a href="<?php echo htmlspecialchars(LINKEDIN_PROFILE_URL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="hpc-linkedin">
          <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
          <span><?php echo __('hpc-li'); ?></span>
        </a>
        <div class="hpc-reach"><?php echo __('hpc-reach'); ?></div>
      </div>
    </div>
  </div>
</section>



<!-- STATEMENT -->
<section class="statement">
  <div class="statement-inner">
    <p class="statement-text"><?php echo __('statement'); ?></p>
    <div class="statement-attr"><?php echo __('statement-attr'); ?></div>
  </div>
</section>

<!-- TRIGGERS -->
<section class="section section-triggers" id="triggers">
  <div class="section-inner">
    <div class="trig-grid">
      <div class="trig-left">
        <div class="eyebrow"><span class="num">01.</span><span><?php echo __('eb-1'); ?></span></div>
        <h2 class="sh"><?php echo __('trig-h'); ?></h2>
        <p class="trig-sub"><?php echo __('trig-sub'); ?></p>
        <a href="<?php echo htmlspecialchars(CALENDLY_BOOKING_URL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="btn-primary"><?php echo __('trig-cta'); ?></a>
      </div>
      <div class="trig-list">
        <div class="trig-item"><div class="trig-n">i</div><div class="trig-t"><strong><?php echo __('trig-1-t'); ?></strong><span><?php echo __('trig-1-d'); ?></span></div></div>
        <div class="trig-item"><div class="trig-n">ii</div><div class="trig-t"><strong><?php echo __('trig-2-t'); ?></strong><span><?php echo __('trig-2-d'); ?></span></div></div>
        <div class="trig-item"><div class="trig-n">iii</div><div class="trig-t"><strong><?php echo __('trig-3-t'); ?></strong><span><?php echo __('trig-3-d'); ?></span></div></div>
        <div class="trig-item"><div class="trig-n">iv</div><div class="trig-t"><strong><?php echo __('trig-4-t'); ?></strong><span><?php echo __('trig-4-d'); ?></span></div></div>
        <div class="trig-item"><div class="trig-n">v</div><div class="trig-t"><strong><?php echo __('trig-5-t'); ?></strong><span><?php echo __('trig-5-d'); ?></span></div></div>
        <div class="trig-item"><div class="trig-n">vi</div><div class="trig-t"><strong><?php echo __('trig-6-t'); ?></strong><span><?php echo __('trig-6-d'); ?></span></div></div>
      </div>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section class="section section-about" id="about">
  <div class="section-inner">
    <div class="about-grid">
      <div class="about-photo-wrap">
        <img class="about-photo" src="img/about.jpeg" alt="<?php echo htmlspecialchars(__('img-alt-about'), ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" decoding="async">
        <div class="about-photo-tag">
          <div class="apt-name"><?php echo __('person-name'); ?></div>
          <div class="apt-role">CRO &middot; Risk Advisor &middot; Fractional CRO</div>
          <div class="apt-line"></div>
          <div class="apt-cred">Former CRO &middot; Allianz &middot; MetLife</div>
        </div>
      </div>
      <div class="about-text">
        <div class="eyebrow"><span class="num">02.</span><span><?php echo __('eb-2'); ?></span></div>
        <h2 class="sh"><?php echo __('about-h'); ?></h2>
        <div class="about-body">
          <p><?php echo __('about-p1'); ?></p>
          <p><?php echo __('about-p2'); ?></p>
          <p><?php echo __('about-p3'); ?></p>
          <p><?php echo __('about-p4'); ?></p>
          <p><?php echo __('about-p5'); ?></p>
        </div>
        <div class="about-credentials">
          <div class="ac-label"><?php echo __('sectors-label'); ?></div>
          <div class="ac-list">
            <div class="ac-item"><?php echo __('sec-1'); ?></div>
            <div class="ac-item"><?php echo __('sec-2'); ?></div>
            <div class="ac-item"><?php echo __('sec-3'); ?></div>
            <div class="ac-item"><?php echo __('sec-4'); ?></div>
            <div class="ac-item"><?php echo __('sec-5'); ?></div>
            <div class="ac-item"><?php echo __('sec-6'); ?></div>
          </div>
        </div>
        <div class="cq">
          <div class="cq-text"><?php echo __('cq-text'); ?></div>
          
        </div>
        <div class="about-stats">
          <div><div class="as-v">15<span class="small">+</span></div><div class="as-l"><?php echo __('about-st-1'); ?></div></div>
          <div><div class="as-v">8<span class="small">+</span></div><div class="as-l"><?php echo __('about-st-2'); ?></div></div>
          <div><div class="as-v">10<span class="small">+</span></div><div class="as-l"><?php echo __('about-st-3'); ?></div></div>
          <div><div class="as-v">2-3<span class="small"> wk</span></div><div class="as-l"><?php echo __('about-st-4'); ?></div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- EXPERTISE -->
<section class="section section-exp" id="expertise">
  <div class="section-inner">
    <div class="exp-hd">
      <div>
        <div class="eyebrow"><span class="num">03.</span><span><?php echo __('eb-3'); ?></span></div>
        <h2 class="sh"><?php echo __('exp-h'); ?></h2>
      </div>
      <p class="exp-intro"><?php echo __('exp-intro'); ?></p>
    </div>
    <div class="exp-grid">

      <div class="ec">
        <div class="ec-head"><span class="ec-n">i.</span><span class="ec-cat"><?php echo __('ec-1-cat'); ?></span></div>
        <h3 class="ec-t"><?php echo __('ec-1-t'); ?></h3>
        <div class="ec-d"><?php echo __('ec-1-d'); ?></div>
        <div class="ec-pull"><?php echo __('ec-1-pull'); ?></div>
      </div>

      <div class="ec">
        <div class="ec-head"><span class="ec-n">ii.</span><span class="ec-cat"><?php echo __('ec-2-cat'); ?></span></div>
        <h3 class="ec-t"><?php echo __('ec-2-t'); ?></h3>
        <div class="ec-d"><?php echo __('ec-2-d'); ?></div>
        <div class="ec-pull"><?php echo __('ec-2-pull'); ?></div>
      </div>

      <div class="ec">
        <div class="ec-head"><span class="ec-n">iii.</span><span class="ec-cat"><?php echo __('ec-3-cat'); ?></span></div>
        <h3 class="ec-t"><?php echo __('ec-3-t'); ?></h3>
        <div class="ec-d"><?php echo __('ec-3-d'); ?></div>
        <div class="ec-pull"><?php echo __('ec-3-pull'); ?></div>
      </div>

      <div class="ec">
        <div class="ec-head"><span class="ec-n">iv.</span><span class="ec-cat"><?php echo __('ec-4-cat'); ?></span></div>
        <h3 class="ec-t"><?php echo __('ec-4-t'); ?></h3>
        <div class="ec-d"><?php echo __('ec-4-d'); ?></div>
        <div class="ec-pull"><?php echo __('ec-4-pull'); ?></div>
      </div>

      <div class="ec">
        <div class="ec-head"><span class="ec-n">v.</span><span class="ec-cat"><?php echo __('ec-5-cat'); ?></span></div>
        <h3 class="ec-t"><?php echo __('ec-5-t'); ?></h3>
        <div class="ec-d"><?php echo __('ec-5-d'); ?></div>
        <div class="ec-pull"><?php echo __('ec-5-pull'); ?></div>
      </div>

      <div class="ec">
        <div class="ec-head"><span class="ec-n">vi.</span><span class="ec-cat"><?php echo __('ec-6-cat'); ?></span></div>
        <h3 class="ec-t"><?php echo __('ec-6-t'); ?></h3>
        <div class="ec-d"><?php echo __('ec-6-d'); ?></div>
        <div class="ec-pull"><?php echo __('ec-6-pull'); ?></div>
      </div>

      <div class="ec ec-feat">
        <div>
          <div class="ec-head"><span class="ec-n">vii.</span><span class="ec-cat"><?php echo __('ec-7-cat'); ?></span></div>
          <h3 class="ec-t"><?php echo __('ec-7-t'); ?></h3>
        </div>
        <div>
          <div class="ec-d"><?php echo __('ec-7-d'); ?></div>
          <div class="ec-pills">
            <span class="ep"><?php echo __('ep-1'); ?></span>
            <span class="ep"><?php echo __('ep-2'); ?></span>
            <span class="ep"><?php echo __('ep-3'); ?></span>
            <span class="ep"><?php echo __('ep-4'); ?></span>
            <span class="ep"><?php echo __('ep-5'); ?></span>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- METHOD -->
<section class="section section-method" id="methodology">
  <div class="section-inner">
    <div class="method-grid">
      <div class="method-l">
        <div class="eyebrow"><span class="num">04.</span><span><?php echo __('eb-4'); ?></span></div>
        <h2 class="sh"><?php echo __('method-h'); ?></h2>
        <p class="method-body"><?php echo __('method-body'); ?></p>
        <a href="<?php echo htmlspecialchars(CALENDLY_BOOKING_URL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="btn-primary"><?php echo __('method-cta'); ?></a>
      </div>
      <div class="method-r">
        <div class="steps">
          <div class="step">
            <div class="step-bar"></div>
            <div class="step-n">i.</div>
            <div class="step-content">
              <h3 class="step-t"><?php echo __('step-1-t'); ?></h3>
            <div class="step-d"><?php echo __('step-1-d'); ?></div>
            </div>
            <div class="step-time"><?php echo __('step-1-time'); ?></div>
          </div>
          <div class="step">
            <div class="step-bar"></div>
            <div class="step-n">ii.</div>
            <div class="step-content">
              <h3 class="step-t"><?php echo __('step-2-t'); ?></h3>
            <div class="step-d"><?php echo __('step-2-d'); ?></div>
            </div>
            <div class="step-time"><?php echo __('step-2-time'); ?></div>
          </div>
          <div class="step">
            <div class="step-bar"></div>
            <div class="step-n">iii.</div>
            <div class="step-content">
              <h3 class="step-t"><?php echo __('step-3-t'); ?></h3>
            <div class="step-d"><?php echo __('step-3-d'); ?></div>
            </div>
            <div class="step-time"><?php echo __('step-3-time'); ?></div>
          </div>
          <div class="step">
            <div class="step-bar"></div>
            <div class="step-n">iv.</div>
            <div class="step-content">
              <h3 class="step-t"><?php echo __('step-4-t'); ?></h3>
            <div class="step-d"><?php echo __('step-4-d'); ?></div>
            </div>
            <div class="step-time"><?php echo __('step-4-time'); ?></div>
          </div>
          <div class="step">
            <div class="step-bar"></div>
            <div class="step-n">v.</div>
            <div class="step-content">
              <h3 class="step-t"><?php echo __('step-5-t'); ?></h3>
            <div class="step-d"><?php echo __('step-5-d'); ?></div>
            </div>
            <div class="step-time"><?php echo __('step-5-time'); ?></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- DELIVERABLES -->
<section class="section-deliv">
  <div class="deliv-inner">
    <div class="deliv-hd">
      <div>
        <div class="eyebrow"><span class="num">05.</span><span><?php echo __('eb-5'); ?></span></div>
        <h2 class="sh"><?php echo __('deliv-h'); ?></h2>
      </div>
      <div class="deliv-hd-r"><?php echo __('deliv-r'); ?></div>
    </div>
    <div class="deliv-grid">
      <div class="di">
        <div class="di-num">01</div>
        <div class="di-label"><?php echo __('di-1-l'); ?></div>
        <h3 class="di-t"><?php echo __('di-1-t'); ?></h3>
        <div class="di-d"><?php echo __('di-1-d'); ?></div>
      </div>
      <div class="di">
        <div class="di-num">02</div>
        <div class="di-label"><?php echo __('di-2-l'); ?></div>
        <h3 class="di-t"><?php echo __('di-2-t'); ?></h3>
        <div class="di-d"><?php echo __('di-2-d'); ?></div>
      </div>
      <div class="di">
        <div class="di-num">03</div>
        <div class="di-label"><?php echo __('di-3-l'); ?></div>
        <h3 class="di-t"><?php echo __('di-3-t'); ?></h3>
        <div class="di-d"><?php echo __('di-3-d'); ?></div>
      </div>
      <div class="di">
        <div class="di-num">04</div>
        <div class="di-label"><?php echo __('di-4-l'); ?></div>
        <h3 class="di-t"><?php echo __('di-4-t'); ?></h3>
        <div class="di-d"><?php echo __('di-4-d'); ?></div>
      </div>
    </div>
  </div>
</section>

<!-- PROOF -->
<section class="section section-proof" id="proof">
  <div class="section-inner">
    <div class="proof-hd">
      <div>
        <div class="eyebrow"><span class="num">06.</span><span><?php echo __('eb-6'); ?></span></div>
        <h2 class="sh"><?php echo __('proof-h'); ?></h2>
      </div>
      <p class="proof-note"><?php echo __('proof-note'); ?></p>
    </div>
    <div class="cases">
      <div class="case">
        <div class="cs-head">
          <div class="cs-num">i.</div>
          <div class="cs"><?php echo __('case-1-cs'); ?></div>
        </div>
        <div class="csit"><?php echo __('case-1-situation'); ?></div>
        <div class="crs">
          <div class="cr"><div class="cn">3<span class="small"> wk</span></div><div class="cd"><?php echo __('case-1-r1-d'); ?></div></div>
          <div class="cr"><div class="cn">100<span class="small">%</span></div><div class="cd"><?php echo __('case-1-r2-d'); ?></div></div>
          <div class="cr"><div class="cn">0</div><div class="cd"><?php echo __('case-1-r3-d'); ?></div></div>
        </div>
        <div class="cf"><?php echo __('case-1-cf'); ?></div>
      </div>
      <div class="case">
        <div class="cs-head">
          <div class="cs-num">ii.</div>
          <div class="cs"><?php echo __('case-2-cs'); ?></div>
        </div>
        <div class="csit"><?php echo __('case-2-situation'); ?></div>
        <div class="crs">
          <div class="cr"><div class="cn">6<span class="small"> wk</span></div><div class="cd"><?php echo __('case-2-r1-d'); ?></div></div>
          <div class="cr"><div class="cn">4</div><div class="cd"><?php echo __('case-2-r2-d'); ?></div></div>
          <div class="cr"><div class="cn">2<span class="small">x</span></div><div class="cd"><?php echo __('case-2-r3-d'); ?></div></div>
        </div>
        <div class="cf"><?php echo __('case-2-cf'); ?></div>
      </div>
      <div class="case">
        <div class="cs-head">
          <div class="cs-num">iii.</div>
          <div class="cs"><?php echo __('case-3-cs'); ?></div>
        </div>
        <div class="csit"><?php echo __('case-3-situation'); ?></div>
        <div class="crs">
          <div class="cr"><div class="cn">12<span class="small"> d</span></div><div class="cd"><?php echo __('case-3-r1-d'); ?></div></div>
          <div class="cr"><div class="cn">0</div><div class="cd"><?php echo __('case-3-r2-d'); ?></div></div>
          <div class="cr"><div class="cn">6<span class="small"> mo</span></div><div class="cd"><?php echo __('case-3-r3-d'); ?></div></div>
        </div>
        <div class="cf"><?php echo __('case-3-cf'); ?></div>
      </div>
    </div>
  </div>
</section>

<!-- INSIGHTS -->
<section class="section section-insights" id="insights">
  <div class="section-inner">
    <div class="ins-hd">
      <div>
        <div class="eyebrow"><span class="num">07.</span><span><?php echo __('eb-7'); ?></span></div>
        <h2 class="sh"><?php echo __('ins-h'); ?></h2>
      </div>
      <div class="tags-row" role="group" aria-label="<?php echo htmlspecialchars(__('ins-tags-aria'), ENT_QUOTES, 'UTF-8'); ?>">
        <button type="button" class="tag ins-tag" data-tag="risk-theater" aria-pressed="false"><?php echo __('ins-tag-risk-theater'); ?></button>
        <button type="button" class="tag ins-tag" data-tag="policy-theater" aria-pressed="false"><?php echo __('ins-tag-policy-theater'); ?></button>
        <button type="button" class="tag ins-tag" data-tag="fraud-governance" aria-pressed="false"><?php echo __('ins-tag-fraud-governance'); ?></button>
        <button type="button" class="tag ins-tag" data-tag="kri-design" aria-pressed="false"><?php echo __('ins-tag-kri-design'); ?></button>
        <button type="button" class="tag ins-tag" data-tag="decision-quality" aria-pressed="false"><?php echo __('ins-tag-decision-quality'); ?></button>
      </div>
    </div>
    <div class="posts">
      <a href="https://www.linkedin.com/posts/elina-moshkovich-41397115_governance-boardeffectiveness-riskmanagement-share-7419806316895895553-YcZz" target="_blank" rel="noopener noreferrer" class="post" data-tags="risk-theater">
        <div class="pc-head">
          <div class="pc"><span><?php echo __('ins-tag-risk-theater'); ?></span></div>
          <div class="p-num">i.</div>
        </div>
        <h3 class="pt"><?php echo __('post-1-t'); ?></h3>
        <div class="px"><?php echo __('post-1-d'); ?></div>
        <div class="pf"><span><?php echo __('post-read'); ?></span><span class="pa">&rarr;</span></div>
      </a>
      <a href="https://www.linkedin.com/posts/elina-moshkovich-41397115_riskmanagement-governance-cro-share-7452290477904691201-qJ_m" target="_blank" rel="noopener noreferrer" class="post" data-tags="kri-design">
        <div class="pc-head">
          <div class="pc"><span><?php echo __('ins-tag-kri-design'); ?></span></div>
          <div class="p-num">ii.</div>
        </div>
        <h3 class="pt"><?php echo __('post-4-t'); ?></h3>
        <div class="px"><?php echo __('post-4-d'); ?></div>
        <div class="pf"><span><?php echo __('post-read'); ?></span><span class="pa">&rarr;</span></div>
      </a>
      <a href="https://www.linkedin.com/posts/elina-moshkovich-41397115_riskmanagement-rm2-governance-share-7454169487253135361--UuZ" target="_blank" rel="noopener noreferrer" class="post" data-tags="decision-quality">
        <div class="pc-head">
          <div class="pc"><span><?php echo __('ins-tag-decision-quality'); ?></span></div>
          <div class="p-num">iii.</div>
        </div>
        <h3 class="pt"><?php echo __('post-5-t'); ?></h3>
        <div class="px"><?php echo __('post-5-d'); ?></div>
        <div class="pf"><span><?php echo __('post-read'); ?></span><span class="pa">&rarr;</span></div>
      </a>
      <a href="https://www.linkedin.com/posts/elina-moshkovich-41397115_riskmanagement-cro-riskculture-share-7429852234730852352-Y9F3" target="_blank" rel="noopener noreferrer" class="post" data-tags="policy-theater">
        <div class="pc-head">
          <div class="pc"><span><?php echo __('ins-tag-policy-theater'); ?></span></div>
          <div class="p-num">iv.</div>
        </div>
        <h3 class="pt"><?php echo __('post-2-t'); ?></h3>
        <div class="px"><?php echo __('post-2-d'); ?></div>
        <div class="pf"><span><?php echo __('post-read'); ?></span><span class="pa">&rarr;</span></div>
      </a>
      <a href="https://www.linkedin.com/posts/elina-moshkovich-41397115_fraud-is-born-in-governance-ugcPost-7445429119082967040-hWhb" target="_blank" rel="noopener noreferrer" class="post" data-tags="fraud-governance">
        <div class="pc-head">
          <div class="pc"><span><?php echo __('ins-tag-fraud-governance'); ?></span></div>
          <div class="p-num">v.</div>
        </div>
        <h3 class="pt"><?php echo __('post-3-t'); ?></h3>
        <div class="px"><?php echo __('post-3-d'); ?></div>
        <div class="pf"><span><?php echo __('post-read'); ?></span><span class="pa">&rarr;</span></div>
      </a>
    </div>
  </div>
</section>



<!-- FEATURED APPEARANCES -->
<section class="section-media" id="media">
  <div class="media-inner">
    <div class="media-hd">
      <div>
        <div class="eyebrow"><span class="num">08.</span><span><?php echo __('eb-media'); ?></span></div>
        <h2 class="sh --<?php echo htmlspecialchars($locale, ENT_QUOTES, 'UTF-8'); ?>"><?php echo __('media-h'); ?></h2>
      </div>
      <p class="media-intro"><?php echo __('media-intro'); ?></p>
    </div>

    <div class="media-grid">

      <a href="https://www.buzzsprout.com/2512103/episodes/18835401" target="_blank" class="media-card media-card-featured">
        <div class="mc-type"><span><?php echo __('mc-1-type'); ?></span></div>
        <div class="mc-platform"><?php echo __('mc-1-platform'); ?></div>
        <h3 class="mc-title"><?php echo __('mc-1-title'); ?></h3>
        <div class="mc-desc"><?php echo __('mc-1-desc'); ?></div>
        <div class="mc-host"><?php echo __('mc-1-host'); ?></div>
        <div class="mc-meta">
          <span><?php echo __('mc-1-date'); ?></span>
          <span class="mc-arrow">&rarr;</span>
        </div>
      </a>

      <div class="media-card media-card-photo">
        <img class="mcp-img" src="img/speaker.jpeg" alt="<?php echo htmlspecialchars(__('img-alt-speaker'), ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" decoding="async">
        <div class="mcp-content">
          <div class="mcp-type"><span><?php echo __('mc-2-type'); ?></span></div>
          <h3 class="mcp-title"><?php echo __('mc-2-title'); ?></h3>
          <div class="mcp-platform"><?php echo __('mc-2-platform'); ?></div>
          <div class="mcp-date"><?php echo __('mc-2-date'); ?></div>
        </div>
      </div>

      <div class="media-card">
        <div class="mc-type"><span><?php echo __('mc-3-type'); ?></span></div>
        <div class="mc-platform"><?php echo __('mc-3-platform'); ?></div>
        <h3 class="mc-title"><?php echo __('mc-3-title'); ?></h3>
        <div class="mc-desc"><?php echo __('mc-3-desc'); ?></div>
        <div class="mc-meta">
          <span><?php echo __('mc-3-date'); ?></span>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- COMMUNITY -->
<section class="community" id="community">
  <div class="community-inner">
    <div class="community-l">
      <div class="eyebrow"><span class="num">09.</span><span><?php echo __('eb-8'); ?></span></div>
      <h2 class="sh --<?php echo htmlspecialchars($locale, ENT_QUOTES, 'UTF-8'); ?>"><?php echo __('comm-h'); ?></h2>
      <p class="community-text"><?php echo __('comm-text'); ?></p>
    </div>
    <div class="community-r">
      <div class="community-features">
        <div class="cf-item">
          <div class="cf-label"><?php echo __('cf-1-l'); ?></div>
          <div class="cf-text"><?php echo __('cf-1-t'); ?></div>
        </div>
        <div class="cf-item">
          <div class="cf-label"><?php echo __('cf-2-l'); ?></div>
          <div class="cf-text"><?php echo __('cf-2-t'); ?></div>
        </div>
        <div class="cf-item">
          <div class="cf-label"><?php echo __('cf-3-l'); ?></div>
          <div class="cf-text"><?php echo __('cf-3-t'); ?></div>
        </div>
        <div class="cf-item">
          <div class="cf-label"><?php echo __('cf-4-l'); ?></div>
          <div class="cf-text"><?php echo __('cf-4-t'); ?></div>
        </div>
      </div>
      <div class="community-audience"><?php echo __('comm-aud'); ?></div>
      <div class="community-aud-list">
        <span class="aud-chip"><?php echo __('aud-1'); ?></span>
        <span class="aud-chip"><?php echo __('aud-2'); ?></span>
        <span class="aud-chip"><?php echo __('aud-3'); ?></span>
        <span class="aud-chip"><?php echo __('aud-4'); ?></span>
      </div>
      <div class="community-cta">
        <a href="<?php echo htmlspecialchars(TELEGRAM_MEMBERSHIP_URL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="btn-tg"><?php echo __('comm-cta'); ?></a>
        <span class="community-note"><?php echo __('comm-note'); ?></span>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="section-cta" id="contact">
  <div class="cta-l">
    <div>
      <div class="cta-l-meta"><span class="cta-l-num">No. 10</span><span><?php echo __('cta-meta'); ?></span></div>
      <h2><?php echo __('cta-h'); ?></h2>
    </div>
    <div class="cta-l-bottom"><?php echo __('cta-bottom'); ?></div>
  </div>
  <div class="cta-r">
    <div class="eyebrow"><span class="num">10.</span><span><?php echo __('eb-9'); ?></span></div>
    <h3><?php echo __('cta-h3'); ?></h3>
    <p class="cta-sub"><?php echo __('cta-sub'); ?></p>
    <div class="cta-actions">
      <a href="<?php echo htmlspecialchars(CALENDLY_BOOKING_URL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="btn-light"><?php echo __('cta-btn-1'); ?></a>
      <a href="<?php echo htmlspecialchars(LINKEDIN_PROFILE_URL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="btn-light-text"><?php echo __('cta-btn-2'); ?></a>
    </div>
    <a href="<?php echo htmlspecialchars(TELEGRAM_CHANNEL_URL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" class="tg-link"><?php echo __('cta-tg'); ?></a>
  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="foot-philosophy">
    <div class="fp-inner">
      <div class="fp-label"><?php echo __('fp-label'); ?></div>
      <div class="fp-text"><?php echo __('fp-text'); ?></div>
    </div>
  </div>
  <div class="foot-main">
    <div class="fm-inner">
      <div class="ft">
        <a href="#top" class="nb">
          <div class="nb-mark">R</div>
          <div class="nb-text">
            <span class="nb-name" style="color:var(--paper);">Risk University</span>
            <span class="nb-sub" style="color:rgba(255,255,255,.5);">Europe &middot; USA &middot; GCC</span>
          </div>
        </a>
        <ul class="fl">
          <li><a href="#triggers"><?php echo __('nav-1'); ?></a></li>
          <li><a href="#about"><?php echo __('nav-2'); ?></a></li>
          <li><a href="#expertise"><?php echo __('nav-3'); ?></a></li>
          <li><a href="#proof"><?php echo __('nav-4'); ?></a></li>
          <li><a href="#insights"><?php echo __('nav-5'); ?></a></li>
          <li><a href="#media"><?php echo __('nav-media'); ?></a></li>
      <li><a href="#community"><?php echo __('nav-6'); ?></a></li>
          <li><a href="<?php echo htmlspecialchars(LINKEDIN_PROFILE_URL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">LinkedIn</a></li>
          <li><a href="<?php echo htmlspecialchars(TELEGRAM_CHANNEL_URL, ENT_QUOTES, 'UTF-8'); ?>" target="_blank">Telegram</a></li>
        </ul>
      </div>
      <div class="fb2">
        <span class="fcp"><?php echo __('footer-copyright'); ?></span>
        <span class="ftg"><?php echo __('footer-tag'); ?></span>
      </div>
    </div>
  </div>
</footer>

<script src="js/main.js" defer></script>


</body>
</html>
