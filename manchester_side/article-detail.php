/* ============================================
   Manchester Side - Article Detail Page
   Modern Sports News Style
   ============================================ */

:root {
    --united-red: #DA291C;
    --united-dark: #9E1B15;
    --city-blue: #6CABDD;
    --city-dark: #1D4ED8;
    --dark: #0F172A;
    --light: #FFFFFF;
    --gray: #94A3B8;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #0F172A 0%, #1E293B 100%);
    color: #FFFFFF;
    line-height: 1.8;
    min-height: 100vh;
}

/* ==================== HEADER ==================== */
header {
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(255,255,255,0.1);
    padding: 1rem 0;
    position: fixed;
    width: 100%;
    top: 0;
    z-index: 1000;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    display: flex;
    align-items: center;
    gap: 10px;
}

.logo h1 {
    font-size: 1.8rem;
    background: linear-gradient(135deg, var(--united-red), var(--city-blue));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 800;
}

nav {
    display: flex;
    align-items: center;
    gap: 2rem;
}

nav > a {
    color: #FFFFFF;
    text-decoration: none;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    transition: all 0.3s ease;
}

nav > a:hover {
    background: rgba(255,255,255,0.1);
}

/* Dropdown */
.dropdown {
    position: relative;
    display: inline-block;
}

.dropdown-toggle {
    color: #FFFFFF;
    font-weight: 600;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.dropdown-toggle:hover {
    background: rgba(255,255,255,0.1);
}

.dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    background: rgba(15, 23, 42, 0.98);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 15px;
    min-width: 250px;
    padding: 0.5rem 0;
    margin-top: 0.5rem;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.dropdown:hover .dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.dropdown-menu a {
    display: block;
    padding: 0.75rem 1.5rem;
    color: #FFFFFF;
    text-decoration: none;
    transition: all 0.3s ease;
    font-weight: 600;
}

.dropdown-menu a:hover {
    background: rgba(255,255,255,0.1);
    padding-left: 2rem;
}

.dropdown-menu a.mu { border-left: 3px solid var(--united-red); }
.dropdown-menu a.city { border-left: 3px solid var(--city-blue); }
.dropdown-menu a.h2h { border-left: 3px solid #FBB024; }

/* ==================== ARTICLE CONTAINER ==================== */
.article-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 100px 20px 40px;
}

.content-wrapper {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 3rem;
}

/* ==================== BREADCRUMB ==================== */
.breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 2rem;
    font-size: 0.9rem;
    color: #CBD5E1;
}

.breadcrumb a {
    color: #CBD5E1;
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb a:hover {
    color: #FFFFFF;
}

.breadcrumb i {
    font-size: 0.7rem;
}

/* ==================== ARTICLE MAIN ==================== */
.article-main {
    background: rgba(255,255,255,0.05);
    border-radius: 20px;
    padding: 3rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.1);
}

/* Article Header */
.article-category {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
    flex-wrap: wrap;
}

.team-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    color: white;
    font-size: 0.85rem;
    font-weight: 700;
    text-transform: uppercase;
}

body.manchester-united .team-badge {
    background: var(--united-red);
}

body.manchester-city .team-badge {
    background: var(--city-blue);
}

.category-badge {
    padding: 0.5rem 1rem;
    border-radius: 25px;
    background: rgba(255,255,255,0.1);
    font-size: 0.85rem;
    font-weight: 700;
}

.article-title {
    font-size: 2.5rem;
    line-height: 1.3;
    margin-bottom: 1.5rem;
    color: #FFFFFF;
    font-weight: 800;
}

body.manchester-united .article-title {
    text-shadow: 2px 2px 10px rgba(218, 41, 28, 0.3);
}

body.manchester-city .article-title {
    text-shadow: 2px 2px 10px rgba(108, 171, 221, 0.3);
}

.article-meta-top {
    display: flex;
    gap: 2rem;
    padding: 1rem 0;
    border-top: 1px solid rgba(255,255,255,0.1);
    border-bottom: 1px solid rgba(255,255,255,0.1);
    margin-bottom: 2rem;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #CBD5E1;
    font-size: 0.9rem;
}

.meta-item i {
    color: var(--gray);
}

/* Featured Image */
.article-featured-image {
    margin: 2rem auto;
    border-radius: 15px;
    overflow: hidden;
    max-width: max( 1500px);
    max-height: 1000px;
    border: 6px solid rgba(255,255,255,0.06);
    box-shadow: 0 12px 36px rgba(0,0,0,0.45);
}

.article-featured-image img {
    width: 100%;
    height: auto;
    display: block;
    max-width: 1500px;
    max-height: 1000px;
    object-fit: contain;
}

/* Slight team tint for the border (subtle) */
body.manchester-united .article-featured-image { border-color: rgba(218,41,28,0.12); }
body.manchester-city .article-featured-image { border-color: rgba(108,171,221,0.12); }

.article-featured-image figcaption {
    padding: 1rem;
    background: rgba(0,0,0,0.3);
    color: #CBD5E1;
    font-size: 0.9rem;
    font-style: italic;
}

/* Article Body */
.article-body {
    font-size: 1.1rem;
    line-height: 1.9;
    color: #E2E8F0;
}

.article-body p {
    margin-bottom: 1.5rem;
}

.article-body p:first-letter {
    font-size: 3rem;
    font-weight: 700;
    float: left;
    line-height: 1;
    margin: 0 0.5rem 0 0;
    color: var(--united-red);
}

body.manchester-city .article-body p:first-letter {
    color: var(--city-blue);
}

/* Article Ad */
.article-ad {
    margin: 2rem 0;
    text-align: center;
}

.ad-label {
    display: block;
    font-size: 0.7rem;
    color: #64748B;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.ad-placeholder {
    background: rgba(255,255,255,0.05);
    border: 2px dashed rgba(255,255,255,0.1);
    border-radius: 10px;
    padding: 3rem;
    color: #64748B;
}

.ad-placeholder i {
    font-size: 3rem;
    margin-bottom: 1rem;
}

/* Article Tags */
.article-tags {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin: 2rem 0;
    padding: 1.5rem 0;
    border-top: 1px solid rgba(255,255,255,0.1);
    flex-wrap: wrap;
}

.article-tags i {
    color: var(--gray);
}

.tag {
    padding: 0.5rem 1rem;
    background: rgba(255,255,255,0.05);
    border-radius: 20px;
    font-size: 0.85rem;
    color: #CBD5E1;
    transition: all 0.3s ease;
}

.tag:hover {
    background: rgba(255,255,255,0.1);
    color: #FFFFFF;
}

/* Social Share */
.article-share {
    margin: 2rem 0;
    padding: 2rem;
    background: rgba(255,255,255,0.03);
    border-radius: 15px;
}

.article-share h3 {
    color: #FFFFFF;
    margin-bottom: 1.5rem;
    font-size: 1.2rem;
}

.share-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.share-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
    color: white;
    font-size: 0.9rem;
}

.share-btn.facebook { background: #1877F2; }
.share-btn.twitter { background: #1DA1F2; }
.share-btn.whatsapp { background: #25D366; }
.share-btn.telegram { background: #0088cc; }
.share-btn.copy { background: var(--gray); }

.share-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

/* ==================== RELATED ARTICLES ==================== */
.related-section {
    margin-top: 3rem;
    padding-top: 3rem;
    border-top: 2px solid rgba(255,255,255,0.1);
}

.section-title {
    font-size: 1.8rem;
    margin-bottom: 2rem;
    color: #FFFFFF;
    font-weight: 700;
}

.related-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.5rem;
}

.related-card {
    background: rgba(255,255,255,0.05);
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid rgba(255,255,255,0.1);
}

.related-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
}

.related-image {
    position: relative;
    height: 100px;
    overflow: hidden;
}

.related-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-overlay {
    position: absolute;
    top: 10px;
    left: 10px;
    padding: 0.4rem 0.8rem;
    background: rgba(0,0,0,0.7);
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
    color: #FFFFFF;
}

.related-content {
    padding: 1.5rem;
}

.related-date {
    display: block;
    color: #94A3B8;
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
}

.related-title a {
    color: #FFFFFF;
    text-decoration: none;
    font-size: 1.1rem;
    font-weight: 600;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.related-title a:hover {
    color: var(--city-blue);
}

/* ==================== SIDEBAR ==================== */
.sidebar {
    display: flex;
    flex-direction: column;
    gap: 2rem;
}

.sidebar-widget {
    background: rgba(255,255,255,0.05);
    border-radius: 15px;
    padding: 1.5rem;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.1);
}

.widget-title {
    font-size: 1.2rem;
    margin-bottom: 1.5rem;
    color: #FFFFFF;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

/* Popular Articles */
.popular-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.popular-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    background: rgba(255,255,255,0.03);
    border-radius: 10px;
    transition: all 0.3s ease;
}

.popular-item:hover {
    background: rgba(255,255,255,0.08);
}

.popular-number {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, var(--united-red), var(--city-blue));
    border-radius: 10px;
    font-weight: 800;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.popular-content {
    flex: 1;
}

.popular-title {
    color: #FFFFFF;
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 600;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.popular-title:hover {
    color: var(--city-blue);
}

.popular-meta {
    display: flex;
    gap: 1rem;
    margin-top: 0.5rem;
    font-size: 0.8rem;
    color: #94A3B8;
}

.popular-team {
    font-weight: 600;
}

/* Team Widget */
.team-widget {
    border-top: 4px solid var(--united-red);
}

body.manchester-city .team-widget {
    border-top-color: var(--city-blue);
}

.team-info p {
    color: #CBD5E1;
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.widget-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    background: var(--united-red);
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

body.manchester-city .widget-btn {
    background: var(--city-blue);
}

.widget-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

/* Ad Widget */
.ad-widget .ad-placeholder {
    padding: 4rem 2rem;
}

/* ==================== FOOTER ==================== */
footer {
    background: rgba(15, 23, 42, 0.95);
    border-top: 1px solid rgba(255,255,255,0.1);
    padding: 3rem 0 2rem;
    margin-top: 4rem;
}

.footer-content {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 3rem;
    margin-bottom: 2rem;
    max-width: 1200px;
    margin: 0 auto 2rem;
    padding: 0 20px;
}

.footer-logo {
    font-size: 1.5rem;
    font-weight: 800;
    background: linear-gradient(135deg, var(--united-red), var(--city-blue));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 1rem;
}

.footer-description {
    color: #CBD5E1;
    line-height: 1.6;
}

.footer-links h4 {
    color: #FFFFFF;
    margin-bottom: 1rem;
    font-weight: 700;
}

.footer-links a {
    display: block;
    color: #CBD5E1;
    text-decoration: none;
    margin-bottom: 0.5rem;
    transition: color 0.3s ease;
}

.footer-links a:hover {
    color: var(--city-blue);
}

.footer-bottom {
    text-align: center;
    padding-top: 2rem;
    border-top: 1px solid rgba(255,255,255,0.1);
    color: #CBD5E1;
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem 20px 0;
}

/* ==================== RESPONSIVE ==================== */
@media (max-width: 1024px) {
    .content-wrapper {
        grid-template-columns: 1fr;
    }
    
    .sidebar {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
    }
}

@media (max-width: 768px) {
    nav { display: none; }
    
    .article-main {
        padding: 2rem 1.5rem;
    }
    
    .article-title {
        font-size: 1.8rem;
    }
    
    .article-meta-top {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .related-grid {
        grid-template-columns: 1fr;
    }
    
    .sidebar {
        grid-template-columns: 1fr;
    }
    
    .share-buttons {
        flex-direction: column;
    }
    
    .share-btn {
        width: 100%;
        justify-content: center;
    }
    
    .footer-content {
        grid-template-columns: 1fr;
    }
}
