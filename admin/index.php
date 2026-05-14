<?php
require_once dirname(__DIR__) . '/includes/auth.php';
admin_session_start();
admin_require_auth();
$admin = admin_current();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>SIDIS Admin</title>
<script src="https://cdn.jsdelivr.net/npm/vue@3/dist/vue.global.prod.js"></script>
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--sidebar:#1a1a2e;--sidebar-hover:#252545;--accent:#772885;--accent2:#8e35a0;--bg:#f0f2f5;--card:#fff;--text:#1a1a1a;--muted:#666;--border:#e2e4e8;--red:#e53935;--green:#43a047}
body{font-family:system-ui,-apple-system,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex}
a{color:inherit;text-decoration:none}
.sidebar{width:240px;background:var(--sidebar);min-height:100vh;display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:100}
.sidebar__logo{padding:24px 20px;border-bottom:1px solid rgba(255,255,255,.1)}
.sidebar__logo svg{color:#fff}
.sidebar__nav{padding:12px 0;flex:1;overflow-y:auto}
.nav-item{display:flex;align-items:center;gap:10px;padding:11px 20px;color:rgba(255,255,255,.7);cursor:pointer;font-size:14px;transition:.15s;border-left:3px solid transparent}
.nav-item:hover{background:var(--sidebar-hover);color:#fff}
.nav-item.active{background:var(--sidebar-hover);color:#fff;border-left-color:var(--accent)}
.nav-item svg{flex-shrink:0;opacity:.7}
.nav-item.active svg,.nav-item:hover svg{opacity:1}
.sidebar__footer{padding:16px 20px;border-top:1px solid rgba(255,255,255,.1);font-size:12px;color:rgba(255,255,255,.5)}
.sidebar__footer a{color:rgba(255,255,255,.7)}
.main{margin-left:240px;flex:1;display:flex;flex-direction:column;min-height:100vh}
.topbar{background:#fff;padding:0 28px;height:60px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid var(--border);position:sticky;top:0;z-index:50}
.topbar__title{font-size:18px;font-weight:600}
.topbar__right{display:flex;align-items:center;gap:12px}
.lang-select{padding:6px 12px;border:1px solid var(--border);border-radius:8px;font-size:13px;cursor:pointer;background:#fff}
.content{padding:28px;flex:1}
/* Cards */
.card{background:var(--card);border-radius:12px;border:1px solid var(--border);overflow:hidden;margin-bottom:20px}
.card__head{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.card__head h2{font-size:15px;font-weight:600}
.card__body{padding:20px}
/* Form */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px}
.form-grid.cols-1{grid-template-columns:1fr}
.form-grid.cols-3{grid-template-columns:1fr 1fr 1fr}
.field{display:flex;flex-direction:column;gap:6px}
.field label{font-size:13px;font-weight:500;color:var(--muted)}
.field input,.field select,.field textarea{padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:14px;font-family:inherit;color:var(--text);background:#fff;transition:border-color .15s;outline:none}
.field input:focus,.field select:focus,.field textarea:focus{border-color:var(--accent)}
.field textarea{resize:vertical;min-height:90px}
.field--full{grid-column:1/-1}
/* Buttons */
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;transition:.15s}
.btn--primary{background:var(--accent);color:#fff}.btn--primary:hover{background:var(--accent2)}
.btn--outline{background:#fff;color:var(--text);border:1px solid var(--border)}.btn--outline:hover{border-color:#aaa}
.btn--danger{background:var(--red);color:#fff}.btn--danger:hover{opacity:.9}
.btn--success{background:var(--green);color:#fff}
.btn--sm{padding:6px 12px;font-size:12px}
.btn--icon{padding:7px;background:none;border:1px solid var(--border);color:var(--muted)}.btn--icon:hover{border-color:#aaa;color:var(--text)}
/* Table */
.table-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
th,td{padding:11px 14px;text-align:left;font-size:13px}
th{background:#f8f9fa;color:var(--muted);font-weight:600;border-bottom:2px solid var(--border)}
td{border-bottom:1px solid var(--border)}
tr:last-child td{border-bottom:none}
tr:hover td{background:#fafafa}
/* Tag */
.tag{display:inline-flex;align-items:center;padding:3px 10px;background:#f0edf5;color:var(--accent);border-radius:20px;font-size:12px;font-weight:500;gap:4px}
/* Alert */
.alert{padding:12px 16px;border-radius:8px;font-size:13px;margin-bottom:16px}
.alert--success{background:#e8f5e9;color:var(--green)}
.alert--error{background:#ffebee;color:var(--red)}
/* Media grid */
.media-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(120px,1fr));gap:12px}
.media-item{border:2px solid var(--border);border-radius:8px;overflow:hidden;cursor:pointer;position:relative}
.media-item:hover{border-color:var(--accent)}
.media-item.selected{border-color:var(--accent)}
.media-item img{width:100%;aspect-ratio:1;object-fit:cover;display:block}
.media-item__name{font-size:11px;padding:4px 6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;background:#f8f9fa}
/* Repeat list */
.repeat-list{display:flex;flex-direction:column;gap:8px}
.repeat-item{background:#f8f9fa;border:1px solid var(--border);border-radius:8px;padding:12px 14px;position:relative}
.repeat-item__actions{position:absolute;top:10px;right:10px;display:flex;gap:6px}
/* Tabs */
.tabs{display:flex;gap:4px;margin-bottom:20px;border-bottom:2px solid var(--border);padding-bottom:0}
.tab-btn{padding:8px 18px;font-size:13px;font-weight:500;border:none;background:none;cursor:pointer;color:var(--muted);border-bottom:2px solid transparent;margin-bottom:-2px;transition:.15s}
.tab-btn.active{color:var(--accent);border-bottom-color:var(--accent)}
/* Modal */
.modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:200;display:flex;align-items:center;justify-content:center;padding:20px}
.modal{background:#fff;border-radius:12px;max-width:700px;width:100%;max-height:90vh;overflow-y:auto}
.modal__head{padding:20px;border-bottom:1px solid var(--border);display:flex;align-items:center;justify-content:space-between}
.modal__head h3{font-size:16px;font-weight:600}
.modal__body{padding:20px}
.modal__foot{padding:16px 20px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px}
/* Status badge */
.badge{display:inline-block;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:600}
.badge--on{background:#e8f5e9;color:var(--green)}
.badge--off{background:#ffebee;color:var(--red)}
/* Image picker thumb */
.img-thumb{width:80px;height:60px;object-fit:cover;border-radius:6px;border:1px solid var(--border)}
.img-picker{display:flex;align-items:center;gap:12px}
/* Slug preview */
.slug-preview{font-size:11px;color:var(--muted);margin-top:3px}
/* Section header */
.section-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
.section-head h1{font-size:22px;font-weight:700}
/* Empty */
.empty{text-align:center;padding:48px 20px;color:var(--muted)}
/* Toggle */
.toggle{position:relative;display:inline-block;width:40px;height:22px}
.toggle input{opacity:0;width:0;height:0}
.toggle__slider{position:absolute;inset:0;background:#ccc;border-radius:22px;cursor:pointer;transition:.2s}
.toggle input:checked + .toggle__slider{background:var(--accent)}
.toggle__slider:before{content:'';position:absolute;height:16px;width:16px;left:3px;bottom:3px;background:#fff;border-radius:50%;transition:.2s}
.toggle input:checked + .toggle__slider:before{transform:translateX(18px)}
/* Upload zone */
.upload-zone{border:2px dashed var(--border);border-radius:8px;padding:24px;text-align:center;cursor:pointer;color:var(--muted);font-size:13px;transition:.15s}
.upload-zone:hover{border-color:var(--accent);color:var(--accent)}
</style>
</head>
<body>
<div id="app">

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
    <div class="sidebar__logo">
        <svg width="90" height="22" viewBox="0 0 115 29" fill="none"><path d="M53.6506 13.8293C53.6506 14.7154 53.3599 15.4805 52.7785 16.1294C52.1971 16.7759 51.3877 17.2736 50.3458 17.6155C49.3062 17.9597 48.1131 18.1318 46.7665 18.1318C46.3897 18.1318 45.9292 18.1132 45.3827 18.076C44.8361 18.0388 44.1105 17.9342 43.2058 17.7644C42.3011 17.5946 41.3592 17.3411 40.3801 17.0085V13.5339C41.2987 13.9758 42.2011 14.3456 43.0872 14.6386C43.9733 14.9316 44.9245 15.0782 45.9385 15.0782C46.8735 15.0782 47.4782 14.9572 47.7549 14.7154C48.0317 14.4735 48.1689 14.2479 48.1689 14.0362C48.1689 13.6548 47.934 13.3339 47.4619 13.0711C46.9898 12.8083 46.3037 12.5385 45.4036 12.2594C44.4082 11.9315 43.5337 11.5756 42.7825 11.1919C42.0313 10.8081 41.4173 10.3244 40.9406 9.74529C40.4638 9.16618 40.2266 8.48242 40.2266 7.69167C40.2266 6.90093 40.4661 6.2381 40.9475 5.62178C41.429 5.00546 42.1616 4.51241 43.15 4.14495C44.1384 3.77748 45.3432 3.59375 46.7688 3.59375C47.7828 3.59375 48.7201 3.6705 49.5806 3.824C50.4411 3.97749 51.1505 4.1496 51.711 4.34031C52.2715 4.53102 52.6552 4.67986 52.8645 4.78917V8.1103C52.1226 7.694 51.2947 7.32421 50.3807 6.99628C49.4667 6.66835 48.4899 6.50555 47.4503 6.50555C46.7688 6.50555 46.2758 6.60789 45.9688 6.81255C45.6618 7.01721 45.5106 7.26839 45.5106 7.56841C45.5106 7.8475 45.6641 8.08937 45.9688 8.29403C46.2734 8.4987 46.82 8.74755 47.6061 9.04059C49.0038 9.55225 50.1225 10.0104 50.9621 10.4197C51.8017 10.8291 52.4575 11.3012 52.9343 11.8361C53.4111 12.371 53.6483 13.0362 53.6483 13.8339Z" fill="white"/><path d="M63.5419 17.9472H58.0625V3.78125H63.5419V17.9472Z" fill="white"/><path d="M3.47226 4.24281L13.3833 0.63547C18.8536-1.35553 24.9108 1.46899 26.9018 6.93921L7.0796 14.1539C4.34339 15.1498 1.31479 13.7375 0.318896 11.0013C-0.676204 8.26731 0.738242 5.23791 3.47226 4.24281Z" fill="white"/><path d="M23.8629 24.4425L13.9518 28.0498C8.48159 30.0408 2.42438 27.2163 0.433387 21.7461L20.2555 14.5314C22.9917 13.5355 26.0203 14.9478 27.0162 17.684C28.0121 20.4202 26.5999 23.4488 23.8637 24.4447Z" fill="#772885"/></svg>
        <div style="font-size:11px;color:rgba(255,255,255,.4);margin-top:4px">Admin Panel</div>
    </div>
    <nav class="sidebar__nav">
        <div v-for="item in navItems" :key="item.id"
             class="nav-item" :class="{active: currentView===item.id}"
             @click="currentView=item.id">
            <span v-html="item.icon"></span>
            {{ item.label }}
        </div>
    </nav>
    <div class="sidebar__footer">
        {{ adminName }}<br>
        <a href="/admin/api/auth.php?action=logout" @click.prevent="logout">Sign out</a>
        &nbsp;·&nbsp;
        <a href="/" target="_blank">↗ Site</a>
    </div>
</aside>

<!-- ══ MAIN ══ -->
<div class="main">
    <div class="topbar">
        <div class="topbar__title">{{ currentViewLabel }}</div>
        <div class="topbar__right">
            <select class="lang-select" v-model="langId" @change="onLangChange">
                <option v-for="l in languages" :key="l.id" :value="l.id">{{ l.name }} ({{ l.code }})</option>
            </select>
            <span style="font-size:13px;color:#999">Lang: {{ currentLangCode }}</span>
        </div>
    </div>

    <div class="content">
        <div v-if="alert.msg" :class="'alert alert--'+alert.type" style="margin-bottom:16px">{{ alert.msg }}</div>

        <!-- ── DASHBOARD ── -->
        <template v-if="currentView==='dashboard'">
            <div class="section-head"><h1>Dashboard</h1></div>
            <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
                <div class="card"><div class="card__body" style="text-align:center">
                    <div style="font-size:32px;font-weight:700;color:var(--accent)">{{ stats.cases }}</div>
                    <div style="font-size:13px;color:var(--muted)">Case Studies</div>
                </div></div>
                <div class="card"><div class="card__body" style="text-align:center">
                    <div style="font-size:32px;font-weight:700;color:var(--accent)">{{ stats.posts }}</div>
                    <div style="font-size:13px;color:var(--muted)">Blog Posts</div>
                </div></div>
                <div class="card"><div class="card__body" style="text-align:center">
                    <div style="font-size:32px;font-weight:700;color:var(--accent)">{{ stats.solutions }}</div>
                    <div style="font-size:13px;color:var(--muted)">Solutions</div>
                </div></div>
                <div class="card"><div class="card__body" style="text-align:center">
                    <div style="font-size:32px;font-weight:700;color:var(--accent)">{{ stats.media }}</div>
                    <div style="font-size:13px;color:var(--muted)">Media Files</div>
                </div></div>
            </div>
            <div class="card"><div class="card__body" style="color:var(--muted);font-size:13px">
                Welcome to SIDIS Admin Panel. Select a section from the sidebar to manage your content.
            </div></div>
        </template>

        <!-- ── LANGUAGES ── -->
        <template v-if="currentView==='languages'">
            <div class="section-head">
                <h1>Languages</h1>
                <button class="btn btn--primary" @click="openLangModal()">+ Add Language</button>
            </div>
            <div class="card">
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>Code</th><th>Name</th><th>Default</th><th>Active</th><th>Actions</th></tr></thead>
                        <tbody>
                            <tr v-for="l in langList" :key="l.id">
                                <td><strong>{{ l.code }}</strong></td>
                                <td>{{ l.name }}</td>
                                <td><span v-if="l.is_default" class="badge badge--on">Default</span><a v-else href="#" @click.prevent="setDefaultLang(l.id)" style="font-size:12px;color:var(--accent)">Set default</a></td>
                                <td><span :class="'badge badge--'+(l.is_active?'on':'off')">{{ l.is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>
                                    <button class="btn btn--outline btn--sm" @click="openLangModal(l)">Edit</button>
                                    <button v-if="!l.is_default" class="btn btn--danger btn--sm" style="margin-left:6px" @click="deleteLang(l)">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <!-- ── HOME PAGE ── -->
        <template v-if="currentView==='home'">
            <div class="section-head"><h1>Home Page</h1>
                <button class="btn btn--primary" @click="saveHome">Save All</button>
            </div>
            <div class="tabs">
                <button v-for="t in ['Hero','Why Slides','Automation','Partner Logos','Reviews','Solutions Section']" :key="t"
                    class="tab-btn" :class="{active:homeTab===t}" @click="homeTab=t">{{ t }}</button>
            </div>

            <!-- Hero -->
            <div v-if="homeTab==='Hero'" class="card"><div class="card__body">
                <div class="form-grid">
                    <div class="field field--full"><label>Hero Title</label><input v-model="homeData.hero_title"></div>
                    <div class="field field--full"><label>Hero Subtitle</label><textarea v-model="homeData.hero_subtitle" rows="3"></textarea></div>
                    <div class="field"><label>Button 1 Text</label><input v-model="homeData.hero_btn1_text"></div>
                    <div class="field"><label>Button 1 URL</label><input v-model="homeData.hero_btn1_url"></div>
                    <div class="field"><label>Button 2 Text</label><input v-model="homeData.hero_btn2_text"></div>
                    <div class="field"><label>Button 2 URL</label><input v-model="homeData.hero_btn2_url"></div>
                    <div class="field field--full"><label>Hero Video Path (e.g. ./assets/video/1-hero.mp4)</label><input v-model="homeData.hero_video_path"></div>
                    <div class="field field--full"><label>CTA Section Title</label><input v-model="homeData.cta_title"></div>
                    <div class="field"><label>CTA Button Text</label><input v-model="homeData.cta_btn_text"></div>
                    <div class="field"><label>CTA Button URL</label><input v-model="homeData.cta_btn_url"></div>
                </div>
            </div></div>

            <!-- Why Slides -->
            <div v-if="homeTab==='Why Slides'">
                <div class="section-head" style="margin-bottom:12px">
                    <span style="font-size:15px;font-weight:600">"Why" Carousel Slides</span>
                    <button class="btn btn--primary btn--sm" @click="openWhySlideModal()">+ Add Slide</button>
                </div>
                <div class="card"><div class="table-wrap"><table>
                    <thead><tr><th>#</th><th>Title</th><th>Actions</th></tr></thead>
                    <tbody>
                        <tr v-for="s in whySlides" :key="s.id">
                            <td>{{ s.sort_order }}</td>
                            <td>{{ s.title }}</td>
                            <td>
                                <button class="btn btn--outline btn--sm" @click="openWhySlideModal(s)">Edit</button>
                                <button class="btn btn--danger btn--sm" style="margin-left:6px" @click="deleteWhySlide(s.id)">Del</button>
                            </td>
                        </tr>
                    </tbody>
                </table></div></div>
            </div>

            <!-- Automation -->
            <div v-if="homeTab==='Automation'" class="card"><div class="card__body">
                <div class="form-grid">
                    <div class="field field--full"><label>Section Title</label><input v-model="homeData.automation_title"></div>
                    <div class="field field--full"><label>Section Text</label><textarea v-model="homeData.automation_text" rows="3"></textarea></div>
                    <div class="field"><label>Location 1 Title (e.g. Estonia)</label><input v-model="homeData.automation_loc1_title"></div>
                    <div class="field"><label>Location 1 City</label><input v-model="homeData.automation_loc1_city"></div>
                    <div class="field"><label>Location 2 Title</label><input v-model="homeData.automation_loc2_title"></div>
                    <div class="field"><label>Location 2 City</label><input v-model="homeData.automation_loc2_city"></div>
                </div>
                <hr style="margin:20px 0;border:none;border-top:1px solid var(--border)">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
                    <strong style="font-size:14px">Automation Images (Carousel)</strong>
                    <button class="btn btn--outline btn--sm" @click="pickMedia(img=>addAutoImage(img))">+ Add Image</button>
                </div>
                <div style="display:flex;gap:10px;flex-wrap:wrap">
                    <div v-for="(img,i) in autoImages" :key="img.id" style="position:relative">
                        <img :src="img.image_url||'/assets/img/placeholder.webp'" style="width:120px;height:90px;object-fit:cover;border-radius:8px;border:1px solid var(--border)">
                        <button class="btn btn--danger btn--sm" style="position:absolute;top:4px;right:4px;padding:2px 6px" @click="deleteAutoImage(img.id)">×</button>
                    </div>
                </div>
            </div></div>

            <!-- Partner Logos -->
            <div v-if="homeTab==='Partner Logos'">
                <div class="section-head" style="margin-bottom:12px">
                    <strong style="font-size:15px;font-weight:600">Partner / Client Logos (Marquee)</strong>
                    <button class="btn btn--primary btn--sm" @click="pickMedia(img=>addPartnerLogo(img))">+ Add Logo</button>
                </div>
                <div class="card"><div class="card__body">
                    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
                        <div v-for="(logo,i) in partnerLogos" :key="logo.id" style="position:relative;text-align:center">
                            <img :src="logo.image_url||''" style="height:50px;max-width:120px;object-fit:contain;border:1px solid var(--border);border-radius:6px;padding:4px">
                            <button class="btn btn--danger btn--sm" style="position:absolute;top:-6px;right:-6px;padding:1px 6px;font-size:11px" @click="deletePartnerLogo(logo.id)">×</button>
                        </div>
                        <div v-if="!partnerLogos.length" style="color:var(--muted);font-size:13px">No logos yet. Click "+ Add Logo"</div>
                    </div>
                </div></div>
            </div>

            <!-- Reviews -->
            <div v-if="homeTab==='Reviews'">
                <div class="section-head" style="margin-bottom:12px">
                    <span style="font-size:15px;font-weight:600">Client Reviews (Carousel)</span>
                    <button class="btn btn--primary btn--sm" @click="openReviewModal()">+ Add Review</button>
                </div>
                <div class="card"><div class="table-wrap"><table>
                    <thead><tr><th>Author</th><th>Quote</th><th>Active</th><th>Actions</th></tr></thead>
                    <tbody>
                        <tr v-for="r in reviews" :key="r.id">
                            <td>{{ r.author_name }}</td>
                            <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ r.quote }}</td>
                            <td><span :class="'badge badge--'+(r.is_active?'on':'off')">{{ r.is_active ? 'Yes' : 'No' }}</span></td>
                            <td>
                                <button class="btn btn--outline btn--sm" @click="openReviewModal(r)">Edit</button>
                                <button class="btn btn--danger btn--sm" style="margin-left:6px" @click="deleteReview(r.id)">Del</button>
                            </td>
                        </tr>
                    </tbody>
                </table></div></div>
            </div>

            <!-- Solutions Section titles -->
            <div v-if="homeTab==='Solutions Section'" class="card"><div class="card__body">
                <div class="form-grid cols-1">
                    <div class="field"><label>Solutions Section Title</label><input v-model="homeData.solutions_title"></div>
                    <div class="field"><label>Projects Section Title</label><input v-model="homeData.projects_title"></div>
                    <div class="field"><label>Projects Button Text</label><input v-model="homeData.projects_btn_text"></div>
                    <div class="field"><label>Projects Button URL</label><input v-model="homeData.projects_btn_url"></div>
                    <div class="field"><label>Reviews Section Title</label><input v-model="homeData.reviews_title"></div>
                    <div class="field"><label>Why Section Title</label><input v-model="homeData.why_title"></div>
                </div>
            </div></div>
        </template>

        <!-- ── NAVIGATION ── -->
        <template v-if="currentView==='nav'">
            <div class="section-head"><h1>Navigation</h1>
                <button class="btn btn--primary" @click="openNavModal()">+ Add Menu Item</button>
            </div>
            <div class="tabs">
                <button class="tab-btn" :class="{active:navLocation==='header'}" @click="navLocation='header';loadNav()">Header</button>
                <button class="tab-btn" :class="{active:navLocation==='footer'}" @click="navLocation='footer';loadNav()">Footer</button>
            </div>
            <div class="card"><div class="table-wrap"><table>
                <thead><tr><th>Title</th><th>URL</th><th>Mega Menu</th><th>Active</th><th>Sort</th><th>Actions</th></tr></thead>
                <tbody>
                    <tr v-for="item in navItems_data" :key="item.id">
                        <td>{{ item.title }}</td>
                        <td style="font-size:12px;color:var(--muted)">{{ item.url }}</td>
                        <td><span v-if="item.has_mega" class="badge badge--on">Yes</span><span v-else>—</span></td>
                        <td><span :class="'badge badge--'+(item.is_active?'on':'off')">{{ item.is_active?'Yes':'No' }}</span></td>
                        <td>{{ item.sort_order }}</td>
                        <td>
                            <button class="btn btn--outline btn--sm" @click="openNavModal(item)">Edit</button>
                            <button v-if="item.has_mega" class="btn btn--outline btn--sm" style="margin-left:4px" @click="openMegaEditor(item)">Mega</button>
                            <button class="btn btn--danger btn--sm" style="margin-left:4px" @click="deleteNavItem(item.id)">Del</button>
                        </td>
                    </tr>
                </tbody>
            </table></div></div>
        </template>

        <!-- ── SOLUTIONS ── -->
        <template v-if="currentView==='solutions'">
            <div class="section-head"><h1>Solutions / Departments / Industries</h1>
                <button class="btn btn--primary" @click="openSolutionItemModal()">+ Add Item</button>
            </div>
            <div class="tabs">
                <button v-for="t in ['solution','department','industry']" :key="t" class="tab-btn"
                    :class="{active:solutionsTab===t}" @click="solutionsTab=t;loadSolutionItems()">
                    {{ t.charAt(0).toUpperCase()+t.slice(1)+'s' }}
                </button>
            </div>
            <div class="card"><div class="table-wrap"><table>
                <thead><tr><th>Icon</th><th>Title</th><th>Active</th><th>Sort</th><th>Actions</th></tr></thead>
                <tbody>
                    <tr v-for="item in solutionItems" :key="item.id">
                        <td><div v-if="item.icon_svg" v-html="item.icon_svg" style="width:32px;height:32px;overflow:hidden"></div></td>
                        <td>{{ item.title }}</td>
                        <td><span :class="'badge badge--'+(item.is_active?'on':'off')">{{ item.is_active?'Yes':'No' }}</span></td>
                        <td>{{ item.sort_order }}</td>
                        <td>
                            <button class="btn btn--outline btn--sm" @click="openSolutionItemModal(item)">Edit</button>
                            <button class="btn btn--danger btn--sm" style="margin-left:6px" @click="deleteSolutionItem(item.id)">Del</button>
                        </td>
                    </tr>
                </tbody>
            </table></div></div>
        </template>

        <!-- ── CASES ── -->
        <template v-if="currentView==='cases'">
            <div class="section-head">
                <h1>Case Studies</h1>
                <button class="btn btn--primary" @click="openCaseEditor()">+ New Case</button>
            </div>
            <div v-if="!editingCase" class="card"><div class="table-wrap"><table>
                <thead><tr><th>Title</th><th>Slug</th><th>Featured</th><th>Active</th><th>Actions</th></tr></thead>
                <tbody>
                    <tr v-for="c in casesList" :key="c.id">
                        <td>{{ c.title || '—' }}</td>
                        <td style="font-size:12px;color:var(--muted)">{{ c.slug }}</td>
                        <td><span :class="'badge badge--'+(c.is_featured?'on':'off')">{{ c.is_featured?'Yes':'No' }}</span></td>
                        <td><span :class="'badge badge--'+(c.is_active?'on':'off')">{{ c.is_active?'Yes':'No' }}</span></td>
                        <td>
                            <button class="btn btn--outline btn--sm" @click="openCaseEditor(c)">Edit</button>
                            <button class="btn btn--danger btn--sm" style="margin-left:6px" @click="deleteCase(c.id)">Del</button>
                        </td>
                    </tr>
                </tbody>
            </table></div></div>

            <!-- Case Editor -->
            <div v-if="editingCase">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
                    <button class="btn btn--outline" @click="editingCase=null;loadCases()">← Back</button>
                    <h2 style="font-size:18px;font-weight:600">{{ caseForm.id ? 'Edit' : 'New' }} Case Study</h2>
                    <button class="btn btn--primary" style="margin-left:auto" @click="saveCase">Save</button>
                </div>
                <div class="tabs">
                    <button v-for="t in ['Basic','Content','Challenges','Tech Stack','Terms & Tags']" :key="t"
                        class="tab-btn" :class="{active:caseTab===t}" @click="caseTab=t">{{ t }}</button>
                </div>

                <div class="card"><div class="card__body">
                    <div v-if="caseTab==='Basic'" class="form-grid">
                        <div class="field field--full"><label>Title</label><input v-model="caseForm.title"></div>
                        <div class="field"><label>Slug (URL)</label><input v-model="caseForm.slug" @input="caseForm.slug=slugify(caseForm.slug)"><div class="slug-preview">/cases/{{ caseForm.slug }}/</div></div>
                        <div class="field"><label>Location</label><input v-model="caseForm.location"></div>
                        <div class="field"><label>Cooperation Period</label><input v-model="caseForm.cooperation_period"></div>
                        <div class="field field--full"><label>Short Description (for listing)</label><textarea v-model="caseForm.description" rows="3"></textarea></div>
                        <div class="field"><label>Featured Image</label>
                            <div class="img-picker"><img v-if="caseForm.image_url" :src="caseForm.image_url" class="img-thumb"><button class="btn btn--outline btn--sm" @click="pickMedia(m=>{caseForm.featured_image_id=m.id;caseForm.image_url=m.url})">Choose</button><button v-if="caseForm.image_url" class="btn btn--sm" @click="caseForm.featured_image_id=null;caseForm.image_url=''">Clear</button></div>
                        </div>
                        <div class="field"><label>Company Logo</label>
                            <div class="img-picker"><img v-if="caseForm.logo_url" :src="caseForm.logo_url" class="img-thumb"><button class="btn btn--outline btn--sm" @click="pickMedia(m=>{caseForm.company_logo_id=m.id;caseForm.logo_url=m.url})">Choose</button><button v-if="caseForm.logo_url" class="btn btn--sm" @click="caseForm.company_logo_id=null;caseForm.logo_url=''">Clear</button></div>
                        </div>
                        <div class="field"><label>Sort Order</label><input type="number" v-model.number="caseForm.sort_order"></div>
                        <div class="field" style="flex-direction:row;align-items:center;gap:10px;padding-top:22px">
                            <label class="toggle"><input type="checkbox" v-model="caseForm.is_active" :true-value="1" :false-value="0"><span class="toggle__slider"></span></label><span style="font-size:14px">Active</span>
                            &nbsp;&nbsp;
                            <label class="toggle"><input type="checkbox" v-model="caseForm.is_featured" :true-value="1" :false-value="0"><span class="toggle__slider"></span></label><span style="font-size:14px">Featured on Home</span>
                        </div>
                    </div>

                    <div v-if="caseTab==='Content'" class="form-grid cols-1">
                        <div class="field"><label>Overview Text</label><textarea v-model="caseForm.overview_text" rows="6"></textarea></div>
                        <div class="field"><label>Key Results (one per line)</label><textarea v-model="caseForm.key_results_text" rows="5" placeholder="Reduced processing time by 60%&#10;Saved 200 hours/month"></textarea></div>
                        <div class="field"><label>Services Used (tags, one per line)</label><textarea v-model="caseForm.services_text" rows="4" placeholder="AI Integration&#10;Process Automation"></textarea></div>
                        <div class="field"><label>Meta Title</label><input v-model="caseForm.meta_title"></div>
                        <div class="field"><label>Meta Description</label><textarea v-model="caseForm.meta_description" rows="2"></textarea></div>
                    </div>

                    <div v-if="caseTab==='Challenges'">
                        <div style="display:flex;justify-content:space-between;margin-bottom:12px">
                            <strong>Challenges</strong>
                            <button class="btn btn--outline btn--sm" @click="caseForm.challenges.push({id:0,title:'',text:''})">+ Add</button>
                        </div>
                        <div class="repeat-list">
                            <div v-for="(ch,i) in caseForm.challenges" :key="i" class="repeat-item">
                                <div class="repeat-item__actions"><button class="btn btn--danger btn--sm btn--icon" @click="caseForm.challenges.splice(i,1)">×</button></div>
                                <div class="form-grid cols-1" style="gap:8px">
                                    <div class="field"><label>Title</label><input v-model="ch.title"></div>
                                    <div class="field"><label>Description</label><textarea v-model="ch.text" rows="3"></textarea></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="caseTab==='Tech Stack'">
                        <div style="display:flex;justify-content:space-between;margin-bottom:12px">
                            <strong>Tech Stack Items</strong>
                            <button class="btn btn--outline btn--sm" @click="caseForm.tech_items.push({name:'',icon_svg:''})">+ Add</button>
                        </div>
                        <div class="repeat-list">
                            <div v-for="(ti,i) in caseForm.tech_items" :key="i" class="repeat-item">
                                <div class="repeat-item__actions"><button class="btn btn--danger btn--sm btn--icon" @click="caseForm.tech_items.splice(i,1)">×</button></div>
                                <div class="form-grid">
                                    <div class="field"><label>Name</label><input v-model="ti.name"></div>
                                    <div class="field"><label>SVG Icon (paste SVG code)</label><textarea v-model="ti.icon_svg" rows="3"></textarea></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="caseTab==='Terms & Tags'">
                        <p style="font-size:13px;color:var(--muted);margin-bottom:16px">Select which taxonomy terms apply to this case study (used for filtering).</p>
                        <div v-for="termType in ['solution','department','industry']" :key="termType" style="margin-bottom:20px">
                            <strong style="font-size:13px;text-transform:capitalize">{{ termType }}s</strong>
                            <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:8px">
                                <label v-for="term in allTerms.filter(t=>t.type===termType)" :key="term.id" style="display:flex;align-items:center;gap:6px;cursor:pointer;padding:4px 10px;background:#f8f9fa;border-radius:6px;font-size:13px">
                                    <input type="checkbox" :value="term.id" v-model="caseForm.term_ids">
                                    {{ term.name }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div></div>
            </div>
        </template>

        <!-- ── BLOG ── -->
        <template v-if="currentView==='blog'">
            <div class="section-head">
                <h1>Blog Posts</h1>
                <button class="btn btn--primary" @click="openPostEditor()">+ New Post</button>
            </div>
            <div v-if="!editingPost" class="card"><div class="table-wrap"><table>
                <thead><tr><th>Title</th><th>Slug</th><th>Author</th><th>Published</th><th>Active</th><th>Actions</th></tr></thead>
                <tbody>
                    <tr v-for="p in postsList" :key="p.id">
                        <td>{{ p.title || '—' }}</td>
                        <td style="font-size:12px;color:var(--muted)">{{ p.slug }}</td>
                        <td>—</td>
                        <td style="font-size:12px">{{ p.published_at ? p.published_at.substr(0,10) : '' }}</td>
                        <td><span :class="'badge badge--'+(p.is_active?'on':'off')">{{ p.is_active?'Yes':'No' }}</span></td>
                        <td>
                            <button class="btn btn--outline btn--sm" @click="openPostEditor(p)">Edit</button>
                            <button class="btn btn--danger btn--sm" style="margin-left:6px" @click="deletePost(p.id)">Del</button>
                        </td>
                    </tr>
                </tbody>
            </table></div></div>

            <!-- Post Editor -->
            <div v-if="editingPost">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
                    <button class="btn btn--outline" @click="destroyTinyMCE();editingPost=null;loadPosts()">← Back</button>
                    <h2 style="font-size:18px;font-weight:600">{{ postForm.id ? 'Edit' : 'New' }} Blog Post</h2>
                    <button class="btn btn--primary" style="margin-left:auto" @click="savePost">Save</button>
                </div>
                <div class="tabs">
                    <button v-for="t in ['Basic','Content','Tags & TOC','SEO']" :key="t"
                        class="tab-btn" :class="{active:postTab===t}" @click="switchPostTab(t)">{{ t }}</button>
                </div>
                <div class="card"><div class="card__body">
                    <div v-if="postTab==='Basic'" class="form-grid">
                        <div class="field field--full"><label>Title</label><input v-model="postForm.title"></div>
                        <div class="field"><label>Slug</label><input v-model="postForm.slug" @input="postForm.slug=slugify(postForm.slug)"><div class="slug-preview">/blog/{{ postForm.slug }}/</div></div>
                        <div class="field"><label>Publish Date</label><input type="datetime-local" v-model="postForm.published_at"></div>
                        <div class="field field--full"><label>Subtitle / Excerpt</label><textarea v-model="postForm.subtitle" rows="3"></textarea></div>
                        <div class="field"><label>Featured Image</label>
                            <div class="img-picker"><img v-if="postForm.image_url" :src="postForm.image_url" class="img-thumb"><button class="btn btn--outline btn--sm" @click="pickMedia(m=>{postForm.featured_image_id=m.id;postForm.image_url=m.url})">Choose</button></div>
                        </div>
                        <div class="field"><label>Author</label>
                            <select v-model="postForm.author_id">
                                <option :value="null">— No author —</option>
                                <option v-for="a in authorsList" :key="a.id" :value="a.id">{{ a.name }}</option>
                            </select>
                        </div>
                        <div class="field" style="flex-direction:row;align-items:center;gap:10px;padding-top:22px">
                            <label class="toggle"><input type="checkbox" v-model="postForm.is_active" :true-value="1" :false-value="0"><span class="toggle__slider"></span></label><span style="font-size:14px">Published</span>
                        </div>
                    </div>

                    <div v-show="postTab==='Content'">
                        <textarea id="tinymce-editor" v-model="postForm.content"></textarea>
                    </div>

                    <div v-if="postTab==='Tags & TOC'">
                        <div style="margin-bottom:20px">
                            <strong style="font-size:14px">Tags (one per line)</strong>
                            <textarea class="field" style="display:block;width:100%;margin-top:8px;padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-family:inherit;font-size:14px" v-model="postForm.tags_text" rows="5" placeholder="Automation&#10;AI&#10;ERP"></textarea>
                        </div>
                        <div>
                            <div style="display:flex;justify-content:space-between;margin-bottom:12px">
                                <strong style="font-size:14px">Table of Contents</strong>
                                <button class="btn btn--outline btn--sm" @click="postForm.toc.push({title:'',anchor:''})">+ Add</button>
                            </div>
                            <div class="repeat-list">
                                <div v-for="(t,i) in postForm.toc" :key="i" class="repeat-item">
                                    <div class="repeat-item__actions"><button class="btn btn--danger btn--sm btn--icon" @click="postForm.toc.splice(i,1)">×</button></div>
                                    <div class="form-grid">
                                        <div class="field"><label>TOC Title</label><input v-model="t.title" @input="t.anchor=t.anchor||slugify(t.title)"></div>
                                        <div class="field"><label>Anchor ID</label><input v-model="t.anchor"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="postTab==='SEO'" class="form-grid cols-1">
                        <div class="field"><label>Meta Title</label><input v-model="postForm.meta_title"></div>
                        <div class="field"><label>Meta Description</label><textarea v-model="postForm.meta_description" rows="3"></textarea></div>
                    </div>
                </div></div>
            </div>
        </template>

        <!-- ── TERMS ── -->
        <template v-if="currentView==='terms'">
            <div class="section-head"><h1>Taxonomy Terms</h1>
                <button class="btn btn--primary" @click="openTermModal()">+ Add Term</button>
            </div>
            <div v-for="termType in ['solution','department','industry']" :key="termType" class="card" style="margin-bottom:16px">
                <div class="card__head"><h2>{{ termType.charAt(0).toUpperCase()+termType.slice(1)+'s' }}</h2></div>
                <div class="table-wrap"><table>
                    <thead><tr><th>Name</th><th>Slug</th><th>Actions</th></tr></thead>
                    <tbody>
                        <tr v-for="t in allTerms.filter(x=>x.type===termType)" :key="t.id">
                            <td>{{ t.name }}</td>
                            <td style="font-size:12px;color:var(--muted)">{{ t.slug }}</td>
                            <td>
                                <button class="btn btn--outline btn--sm" @click="openTermModal(t)">Edit</button>
                                <button class="btn btn--danger btn--sm" style="margin-left:6px" @click="deleteTerm(t.id)">Del</button>
                            </td>
                        </tr>
                    </tbody>
                </table></div>
            </div>
        </template>

        <!-- ── AUTHORS ── -->
        <template v-if="currentView==='authors'">
            <div class="section-head"><h1>Authors</h1>
                <button class="btn btn--primary" @click="openAuthorModal()">+ Add Author</button>
            </div>
            <div class="card"><div class="table-wrap"><table>
                <thead><tr><th>Photo</th><th>Name</th><th>Title</th><th>Actions</th></tr></thead>
                <tbody>
                    <tr v-for="a in authorsList" :key="a.id">
                        <td><img v-if="a.image_url" :src="a.image_url" style="width:36px;height:36px;border-radius:50%;object-fit:cover"></td>
                        <td>{{ a.name }}</td>
                        <td>{{ a.author_title }}</td>
                        <td>
                            <button class="btn btn--outline btn--sm" @click="openAuthorModal(a)">Edit</button>
                            <button class="btn btn--danger btn--sm" style="margin-left:6px" @click="deleteAuthor(a.id)">Del</button>
                        </td>
                    </tr>
                </tbody>
            </table></div></div>
        </template>

        <!-- ── MEDIA ── -->
        <template v-if="currentView==='media'">
            <div class="section-head"><h1>Media Library</h1>
                <label class="btn btn--primary" style="cursor:pointer">
                    + Upload <input type="file" accept="image/*" multiple style="display:none" @change="uploadFiles">
                </label>
            </div>
            <div class="media-grid">
                <div v-for="m in mediaList" :key="m.id" class="media-item">
                    <img :src="m.url" :alt="m.alt_text" loading="lazy">
                    <div class="media-item__name">{{ m.original_name }}</div>
                    <div style="display:flex;gap:4px;padding:4px 6px;background:#f8f9fa">
                        <button class="btn btn--outline btn--sm" style="flex:1;padding:3px" @click="copyUrl(m.url)">Copy URL</button>
                        <button class="btn btn--danger btn--sm" style="padding:3px 8px" @click="deleteMedia(m.id)">×</button>
                    </div>
                </div>
            </div>
        </template>

        <!-- ── SETTINGS ── -->
        <template v-if="currentView==='settings'">
            <div class="section-head"><h1>Settings</h1>
                <button class="btn btn--primary" @click="saveSettings">Save</button>
            </div>
            <div class="card"><div class="card__head"><h2>Global Settings</h2></div><div class="card__body">
                <div class="form-grid">
                    <div class="field"><label>Site Name</label><input v-model="settingsData.site_name"></div>
                    <div class="field"><label>Contact Email</label><input v-model="settingsData.site_email"></div>
                    <div class="field"><label>LinkedIn URL</label><input v-model="settingsData.social_linkedin"></div>
                    <div class="field"><label>WhatsApp Number</label><input v-model="settingsData.social_whatsapp"></div>
                </div>
            </div></div>
            <div class="card"><div class="card__head"><h2>Language-specific ({{ currentLangCode }})</h2></div><div class="card__body">
                <div class="form-grid cols-1">
                    <div class="field"><label>Site Description (meta)</label><textarea v-model="settingsData.site_description" rows="2"></textarea></div>
                    <div class="field"><label>Footer Copyright</label><input v-model="settingsData.footer_copyright"></div>
                    <div class="field"><label>Contact Address</label><input v-model="settingsData.contact_address"></div>
                    <div class="field"><label>Contact Phone</label><input v-model="settingsData.contact_phone"></div>
                </div>
            </div></div>
        </template>
    </div>
</div>

<!-- ══ MODALS ══ -->

<!-- Language Modal -->
<div v-if="modals.lang" class="modal-overlay" @click.self="modals.lang=false">
    <div class="modal">
        <div class="modal__head"><h3>{{ langForm.id ? 'Edit' : 'Add' }} Language</h3><button class="btn btn--icon" @click="modals.lang=false">×</button></div>
        <div class="modal__body">
            <div class="form-grid cols-1">
                <div class="field"><label>Language Code (e.g. en, uk, de)</label><input v-model="langForm.code" :disabled="!!langForm.id" placeholder="en"></div>
                <div class="field"><label>Language Name</label><input v-model="langForm.name" placeholder="English"></div>
                <div class="field" style="flex-direction:row;align-items:center;gap:10px">
                    <label class="toggle"><input type="checkbox" v-model="langForm.is_active" :true-value="1" :false-value="0"><span class="toggle__slider"></span></label>
                    <span style="font-size:14px">Active</span>
                </div>
            </div>
        </div>
        <div class="modal__foot">
            <button class="btn btn--outline" @click="modals.lang=false">Cancel</button>
            <button class="btn btn--primary" @click="saveLang">Save</button>
        </div>
    </div>
</div>

<!-- Why Slide Modal -->
<div v-if="modals.whySlide" class="modal-overlay" @click.self="modals.whySlide=false">
    <div class="modal">
        <div class="modal__head"><h3>{{ whySlideForm.id ? 'Edit' : 'Add' }} Why Slide</h3><button class="btn btn--icon" @click="modals.whySlide=false">×</button></div>
        <div class="modal__body">
            <div class="form-grid cols-1">
                <div class="field"><label>Sort Order</label><input type="number" v-model.number="whySlideForm.sort_order"></div>
                <div class="field"><label>Title</label><input v-model="whySlideForm.title"></div>
                <div class="field"><label>Text</label><textarea v-model="whySlideForm.text" rows="4"></textarea></div>
                <div class="field"><label>Icon SVG (paste SVG code)</label><textarea v-model="whySlideForm.icon_svg" rows="5" placeholder="<svg ...>...</svg>"></textarea></div>
            </div>
        </div>
        <div class="modal__foot">
            <button class="btn btn--outline" @click="modals.whySlide=false">Cancel</button>
            <button class="btn btn--primary" @click="saveWhySlide">Save</button>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div v-if="modals.review" class="modal-overlay" @click.self="modals.review=false">
    <div class="modal">
        <div class="modal__head"><h3>{{ reviewForm.id ? 'Edit' : 'Add' }} Review</h3><button class="btn btn--icon" @click="modals.review=false">×</button></div>
        <div class="modal__body">
            <div class="form-grid cols-1">
                <div class="field"><label>Quote (short headline)</label><input v-model="reviewForm.quote"></div>
                <div class="field"><label>Full Review Text</label><textarea v-model="reviewForm.text" rows="5"></textarea></div>
                <div class="field"><label>Author Name</label><input v-model="reviewForm.author_name"></div>
                <div class="field"><label>Author Title / Position</label><input v-model="reviewForm.author_title"></div>
                <div class="field"><label>LinkedIn URL</label><input v-model="reviewForm.linkedin_url"></div>
                <div class="field"><label>Author Photo</label>
                    <div class="img-picker"><img v-if="reviewForm.author_image_url" :src="reviewForm.author_image_url" class="img-thumb"><button class="btn btn--outline btn--sm" @click="pickMedia(m=>{reviewForm.author_image_id=m.id;reviewForm.author_image_url=m.url})">Choose</button></div>
                </div>
                <div class="field"><label>Sort Order</label><input type="number" v-model.number="reviewForm.sort_order"></div>
                <div class="field" style="flex-direction:row;align-items:center;gap:10px">
                    <label class="toggle"><input type="checkbox" v-model="reviewForm.is_active" :true-value="1" :false-value="0"><span class="toggle__slider"></span></label><span style="font-size:14px">Active</span>
                </div>
            </div>
        </div>
        <div class="modal__foot">
            <button class="btn btn--outline" @click="modals.review=false">Cancel</button>
            <button class="btn btn--primary" @click="saveReview">Save</button>
        </div>
    </div>
</div>

<!-- Nav Item Modal -->
<div v-if="modals.nav" class="modal-overlay" @click.self="modals.nav=false">
    <div class="modal">
        <div class="modal__head"><h3>{{ navForm.id ? 'Edit' : 'Add' }} Menu Item</h3><button class="btn btn--icon" @click="modals.nav=false">×</button></div>
        <div class="modal__body">
            <div class="form-grid cols-1">
                <div class="field"><label>Title</label><input v-model="navForm.title"></div>
                <div class="field"><label>URL</label><input v-model="navForm.url"></div>
                <div class="field"><label>Location</label><select v-model="navForm.location"><option value="header">Header</option><option value="footer">Footer</option></select></div>
                <div class="field"><label>Target</label><select v-model="navForm.target"><option value="_self">Same tab</option><option value="_blank">New tab</option></select></div>
                <div class="field"><label>Sort Order</label><input type="number" v-model.number="navForm.sort_order"></div>
                <div class="field" style="flex-direction:row;align-items:center;gap:16px">
                    <label class="toggle"><input type="checkbox" v-model="navForm.is_active" :true-value="1" :false-value="0"><span class="toggle__slider"></span></label><span>Active</span>
                    &nbsp;
                    <label class="toggle"><input type="checkbox" v-model="navForm.has_mega" :true-value="1" :false-value="0"><span class="toggle__slider"></span></label><span>Mega Menu</span>
                </div>
            </div>
        </div>
        <div class="modal__foot">
            <button class="btn btn--outline" @click="modals.nav=false">Cancel</button>
            <button class="btn btn--primary" @click="saveNavItem">Save</button>
        </div>
    </div>
</div>

<!-- Mega Menu Editor Modal -->
<div v-if="modals.mega" class="modal-overlay" @click.self="modals.mega=false">
    <div class="modal" style="max-width:900px">
        <div class="modal__head"><h3>Mega Menu: {{ megaNavItem?.title }}</h3><button class="btn btn--icon" @click="modals.mega=false">×</button></div>
        <div class="modal__body">
            <div v-for="cat in megaCategories" :key="cat.id" class="card" style="margin-bottom:12px">
                <div class="card__head">
                    <div style="display:flex;align-items:center;gap:10px;flex:1">
                        <input v-model="cat.title" style="flex:1;padding:6px 10px;border:1px solid var(--border);border-radius:6px;font-size:14px">
                        <input v-model="cat.description" placeholder="Description" style="flex:2;padding:6px 10px;border:1px solid var(--border);border-radius:6px;font-size:14px">
                    </div>
                    <div style="display:flex;gap:6px;margin-left:12px">
                        <button class="btn btn--success btn--sm" @click="saveMegaCategory(cat)">Save</button>
                        <button class="btn btn--danger btn--sm" @click="deleteMegaCategory(cat.id)">Del</button>
                    </div>
                </div>
                <div class="card__body">
                    <div class="repeat-list">
                        <div v-for="(sub,si) in cat.subitems" :key="sub.id" class="repeat-item">
                            <div class="repeat-item__actions"><button class="btn btn--danger btn--sm btn--icon" @click="deleteMegaSubitem(sub.id,cat)">×</button></div>
                            <div class="form-grid">
                                <div class="field"><label>Title</label><input v-model="sub.title"></div>
                                <div class="field"><label>URL</label><input v-model="sub.url"></div>
                                <div class="field field--full"><label>Description</label><input v-model="sub.description"></div>
                                <div class="field field--full"><label>Icon SVG</label><textarea v-model="sub.icon_svg" rows="2"></textarea></div>
                            </div>
                            <button class="btn btn--success btn--sm" style="margin-top:8px" @click="saveMegaSubitem(sub,cat)">Save sub-item</button>
                        </div>
                    </div>
                    <button class="btn btn--outline btn--sm" style="margin-top:8px" @click="cat.subitems.push({id:0,title:'',description:'',url:'#',icon_svg:'',sort_order:cat.subitems.length})">+ Add Sub-item</button>
                </div>
            </div>
            <button class="btn btn--primary btn--sm" @click="megaCategories.push({id:0,nav_item_id:megaNavItem.id,title:'New Category',description:'',sort_order:megaCategories.length,subitems:[]})">+ Add Category</button>
        </div>
        <div class="modal__foot"><button class="btn btn--outline" @click="modals.mega=false">Close</button></div>
    </div>
</div>

<!-- Solution Item Modal -->
<div v-if="modals.solutionItem" class="modal-overlay" @click.self="modals.solutionItem=false">
    <div class="modal">
        <div class="modal__head"><h3>{{ solutionItemForm.id ? 'Edit' : 'Add' }} Item</h3><button class="btn btn--icon" @click="modals.solutionItem=false">×</button></div>
        <div class="modal__body">
            <div class="form-grid cols-1">
                <div class="field"><label>Type</label><select v-model="solutionItemForm.type"><option value="solution">Solution</option><option value="department">Department</option><option value="industry">Industry</option></select></div>
                <div class="field"><label>Title</label><input v-model="solutionItemForm.title"></div>
                <div class="field"><label>Description</label><textarea v-model="solutionItemForm.description" rows="3"></textarea></div>
                <div class="field"><label>Button Text</label><input v-model="solutionItemForm.btn_text"></div>
                <div class="field"><label>Button URL</label><input v-model="solutionItemForm.btn_url"></div>
                <div class="field"><label>Icon SVG</label><textarea v-model="solutionItemForm.icon_svg" rows="4" placeholder="<svg ...>...</svg>"></textarea></div>
                <div class="field"><label>Sort Order</label><input type="number" v-model.number="solutionItemForm.sort_order"></div>
            </div>
        </div>
        <div class="modal__foot">
            <button class="btn btn--outline" @click="modals.solutionItem=false">Cancel</button>
            <button class="btn btn--primary" @click="saveSolutionItem">Save</button>
        </div>
    </div>
</div>

<!-- Term Modal -->
<div v-if="modals.term" class="modal-overlay" @click.self="modals.term=false">
    <div class="modal">
        <div class="modal__head"><h3>{{ termForm.id ? 'Edit' : 'Add' }} Term</h3><button class="btn btn--icon" @click="modals.term=false">×</button></div>
        <div class="modal__body">
            <div class="form-grid cols-1">
                <div class="field"><label>Type</label><select v-model="termForm.type"><option value="solution">Solution</option><option value="department">Department</option><option value="industry">Industry</option></select></div>
                <div class="field"><label>Name</label><input v-model="termForm.name"></div>
                <div class="field"><label>Slug (URL-friendly)</label><input v-model="termForm.slug" @input="termForm.slug=slugify(termForm.slug)"></div>
                <div class="field"><label>Sort Order</label><input type="number" v-model.number="termForm.sort_order"></div>
            </div>
        </div>
        <div class="modal__foot">
            <button class="btn btn--outline" @click="modals.term=false">Cancel</button>
            <button class="btn btn--primary" @click="saveTerm">Save</button>
        </div>
    </div>
</div>

<!-- Author Modal -->
<div v-if="modals.author" class="modal-overlay" @click.self="modals.author=false">
    <div class="modal">
        <div class="modal__head"><h3>{{ authorForm.id ? 'Edit' : 'Add' }} Author</h3><button class="btn btn--icon" @click="modals.author=false">×</button></div>
        <div class="modal__body">
            <div class="form-grid cols-1">
                <div class="field"><label>Name</label><input v-model="authorForm.name"></div>
                <div class="field"><label>Title / Position</label><input v-model="authorForm.title"></div>
                <div class="field"><label>LinkedIn URL</label><input v-model="authorForm.linkedin_url"></div>
                <div class="field"><label>Photo</label>
                    <div class="img-picker"><img v-if="authorForm.image_url" :src="authorForm.image_url" class="img-thumb"><button class="btn btn--outline btn--sm" @click="pickMedia(m=>{authorForm.image_id=m.id;authorForm.image_url=m.url})">Choose</button></div>
                </div>
            </div>
        </div>
        <div class="modal__foot">
            <button class="btn btn--outline" @click="modals.author=false">Cancel</button>
            <button class="btn btn--primary" @click="saveAuthor">Save</button>
        </div>
    </div>
</div>

<!-- Media Picker Modal -->
<div v-if="modals.mediaPicker" class="modal-overlay" @click.self="modals.mediaPicker=false">
    <div class="modal" style="max-width:900px">
        <div class="modal__head"><h3>Choose Image</h3><button class="btn btn--icon" @click="modals.mediaPicker=false">×</button></div>
        <div class="modal__body">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px">
                <label class="btn btn--outline" style="cursor:pointer">+ Upload New <input type="file" accept="image/*" style="display:none" @change="uploadAndPick"></label>
            </div>
            <div class="media-grid">
                <div v-for="m in mediaList" :key="m.id" class="media-item" @click="selectMedia(m)">
                    <img :src="m.url" :alt="m.alt_text" loading="lazy">
                    <div class="media-item__name">{{ m.original_name }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

</div><!-- #app -->

<script>
const { createApp, ref, reactive, computed, onMounted, nextTick } = Vue;

createApp({
    setup() {
        const adminName = ref('<?= addslashes($admin['name'] ?? 'Admin') ?>');
        const currentView = ref('dashboard');
        const langId = ref(1);
        const languages = ref([]);
        const langList = ref([]);
        const alert = reactive({msg:'', type:'success'});
        const stats = reactive({cases:0, posts:0, solutions:0, media:0});

        // Nav sidebar items
        const navItems = [
            {id:'dashboard', label:'Dashboard', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>'},
            {id:'home', label:'Home Page', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>'},
            {id:'nav', label:'Navigation', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>'},
            {id:'solutions', label:'Solutions', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>'},
            {id:'cases', label:'Case Studies', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'},
            {id:'blog', label:'Blog', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 013 3L7 19l-4 1 1-4z"/></svg>'},
            {id:'authors', label:'Authors', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>'},
            {id:'terms', label:'Taxonomy', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4H8.12A5.37 5.37 0 0012 6a5.37 5.37 0 00-3.88 2H20v-4z"/><path d="M20 14H8.12A5.37 5.37 0 0012 16a5.37 5.37 0 00-3.88 2H20v-4z"/></svg>'},
            {id:'media', label:'Media Library', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>'},
            {id:'languages', label:'Languages', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>'},
            {id:'settings', label:'Settings', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>'},
        ];

        const currentViewLabel = computed(() => navItems.find(n=>n.id===currentView.value)?.label || '');
        const currentLangCode = computed(() => languages.value.find(l=>l.id===langId.value)?.code || 'en');

        /* ─── Utils ─────────────────────────────────────────────────────── */
        const api = async (url, opts={}) => {
            const res = await fetch(url, {headers:{'Content-Type':'application/json'},...opts});
            return res.json();
        };
        const showAlert = (msg, type='success') => {
            alert.msg = msg; alert.type = type;
            setTimeout(() => alert.msg='', 4000);
        };
        const slugify = s => s.toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-|-$/g,'');

        /* ─── Languages ─────────────────────────────────────────────────── */
        const langForm = reactive({id:0, code:'', name:'', is_active:1});
        const modals = reactive({lang:false, whySlide:false, review:false, nav:false, mega:false,
                                  solutionItem:false, term:false, author:false, mediaPicker:false});

        const loadLanguages = async () => {
            languages.value = await api('/admin/api/languages.php');
            langList.value = [...languages.value];
            if (languages.value.length && !languages.value.find(l=>l.id===langId.value)) {
                langId.value = languages.value.find(l=>l.is_default)?.id || languages.value[0]?.id;
            }
        };
        const openLangModal = (l=null) => { Object.assign(langForm,l?{...l}:{id:0,code:'',name:'',is_active:1}); modals.lang=true; };
        const saveLang = async () => {
            const r = await api(`/admin/api/languages.php${langForm.id?'?id='+langForm.id:''}`,{method:'POST',body:JSON.stringify(langForm)});
            if (r.error) { showAlert(r.error,'error'); return; }
            modals.lang=false; await loadLanguages(); showAlert('Saved!');
        };
        const deleteLang = async l => {
            if (!confirm(`Delete language "${l.name}"?`)) return;
            await api(`/admin/api/languages.php?action=delete&id=${l.id}`,{method:'POST'});
            await loadLanguages(); showAlert('Deleted');
        };
        const setDefaultLang = async id => {
            await api(`/admin/api/languages.php?action=set_default&id=${id}`,{method:'POST'});
            await loadLanguages(); showAlert('Default language updated');
        };

        /* ─── Home Page ─────────────────────────────────────────────────── */
        const homeTab = ref('Hero');
        const homeData = reactive({});
        const whySlides = ref([]);
        const partnerLogos = ref([]);
        const autoImages = ref([]);
        const reviews = ref([]);
        const whySlideForm = reactive({id:0,sort_order:0,title:'',text:'',icon_svg:''});
        const reviewForm = reactive({id:0,sort_order:0,quote:'',text:'',author_name:'',author_title:'',linkedin_url:'',author_image_id:null,author_image_url:'',is_active:1});

        const loadHome = async () => {
            const d = await api(`/admin/api/home.php?lang_id=${langId.value}`);
            Object.assign(homeData, d);
            whySlides.value = await api(`/admin/api/home.php?action=why_slides&lang_id=${langId.value}`);
            partnerLogos.value = await api(`/admin/api/home.php?action=partner_logos&lang_id=${langId.value}`);
            autoImages.value = await api(`/admin/api/home.php?action=auto_images&lang_id=${langId.value}`);
            reviews.value = await api(`/admin/api/home.php?action=reviews&lang_id=${langId.value}`);
        };
        const saveHome = async () => {
            await api(`/admin/api/home.php?action=save_content&lang_id=${langId.value}`,{method:'POST',body:JSON.stringify(homeData)});
            showAlert('Home page saved!');
        };
        const openWhySlideModal = (s=null) => { Object.assign(whySlideForm,s?{...s}:{id:0,sort_order:whySlides.value.length,title:'',text:'',icon_svg:''}); modals.whySlide=true; };
        const saveWhySlide = async () => {
            await api(`/admin/api/home.php?action=save_why_slide&lang_id=${langId.value}${whySlideForm.id?'&id='+whySlideForm.id:''}`,{method:'POST',body:JSON.stringify(whySlideForm)});
            modals.whySlide=false; whySlides.value = await api(`/admin/api/home.php?action=why_slides&lang_id=${langId.value}`); showAlert('Saved!');
        };
        const deleteWhySlide = async id => {
            if (!confirm('Delete?')) return;
            await api(`/admin/api/home.php?action=delete_why_slide&id=${id}&lang_id=${langId.value}`,{method:'POST'});
            whySlides.value = await api(`/admin/api/home.php?action=why_slides&lang_id=${langId.value}`);
        };
        const openReviewModal = (r=null) => { Object.assign(reviewForm,r?{...r,author_image_url:r.author_image_url||''}:{id:0,sort_order:reviews.value.length,quote:'',text:'',author_name:'',author_title:'',linkedin_url:'',author_image_id:null,author_image_url:'',is_active:1}); modals.review=true; };
        const saveReview = async () => {
            await api(`/admin/api/home.php?action=save_review&lang_id=${langId.value}${reviewForm.id?'&id='+reviewForm.id:''}`,{method:'POST',body:JSON.stringify(reviewForm)});
            modals.review=false; reviews.value = await api(`/admin/api/home.php?action=reviews&lang_id=${langId.value}`); showAlert('Saved!');
        };
        const deleteReview = async id => { if (!confirm('Delete?')) return; await api(`/admin/api/home.php?action=delete_review&id=${id}`,{method:'POST'}); reviews.value = await api(`/admin/api/home.php?action=reviews&lang_id=${langId.value}`); };
        const addPartnerLogo = async img => { await api(`/admin/api/home.php?action=save_partner_logo&lang_id=${langId.value}`,{method:'POST',body:JSON.stringify({image_id:img.id,alt_text:img.alt_text||'',sort_order:partnerLogos.value.length})}); partnerLogos.value = await api(`/admin/api/home.php?action=partner_logos&lang_id=${langId.value}`); };
        const deletePartnerLogo = async id => { await api(`/admin/api/home.php?action=delete_partner_logo&id=${id}`,{method:'POST'}); partnerLogos.value = await api(`/admin/api/home.php?action=partner_logos&lang_id=${langId.value}`); };
        const addAutoImage = async img => { await api(`/admin/api/home.php?action=save_auto_image&lang_id=${langId.value}`,{method:'POST',body:JSON.stringify({image_id:img.id,sort_order:autoImages.value.length})}); autoImages.value = await api(`/admin/api/home.php?action=auto_images&lang_id=${langId.value}`); };
        const deleteAutoImage = async id => { await api(`/admin/api/home.php?action=delete_auto_image&id=${id}`,{method:'POST'}); autoImages.value = await api(`/admin/api/home.php?action=auto_images&lang_id=${langId.value}`); };

        /* ─── Navigation ────────────────────────────────────────────────── */
        const navLocation = ref('header');
        const navItems_data = ref([]);
        const navForm = reactive({id:0,title:'',url:'#',location:'header',target:'_self',sort_order:0,is_active:1,has_mega:0});
        const megaNavItem = ref(null);
        const megaCategories = ref([]);

        const loadNav = async () => {
            navItems_data.value = await api(`/admin/api/nav.php?lang_id=${langId.value}&location=${navLocation.value}`);
        };
        const openNavModal = (item=null) => { Object.assign(navForm,item?{...item}:{id:0,title:'',url:'#',location:navLocation.value,target:'_self',sort_order:navItems_data.value.length,is_active:1,has_mega:0}); modals.nav=true; };
        const saveNavItem = async () => {
            await api(`/admin/api/nav.php?action=save_item&lang_id=${langId.value}${navForm.id?'&id='+navForm.id:''}`,{method:'POST',body:JSON.stringify(navForm)});
            modals.nav=false; await loadNav(); showAlert('Saved!');
        };
        const deleteNavItem = async id => { if (!confirm('Delete?')) return; await api(`/admin/api/nav.php?action=delete_item&id=${id}`,{method:'POST'}); await loadNav(); };
        const openMegaEditor = async item => {
            megaNavItem.value = item;
            megaCategories.value = await api(`/admin/api/nav.php?action=mega_categories&id=${item.id}&lang_id=${langId.value}`);
            modals.mega=true;
        };
        const saveMegaCategory = async cat => {
            await api(`/admin/api/nav.php?action=save_mega_category&lang_id=${langId.value}${cat.id?'&id='+cat.id:''}`,{method:'POST',body:JSON.stringify(cat)});
            megaCategories.value = await api(`/admin/api/nav.php?action=mega_categories&id=${megaNavItem.value.id}&lang_id=${langId.value}`);
            showAlert('Category saved');
        };
        const deleteMegaCategory = async id => { if(!confirm('Delete category and all its sub-items?')) return; await api(`/admin/api/nav.php?action=delete_mega_category&id=${id}`,{method:'POST'}); megaCategories.value = await api(`/admin/api/nav.php?action=mega_categories&id=${megaNavItem.value.id}&lang_id=${langId.value}`); };
        const saveMegaSubitem = async (sub, cat) => { await api(`/admin/api/nav.php?action=save_mega_subitem&lang_id=${langId.value}${sub.id?'&id='+sub.id:''}`,{method:'POST',body:JSON.stringify({...sub,category_id:cat.id})}); showAlert('Sub-item saved'); };
        const deleteMegaSubitem = async (id,cat) => { if(!confirm('Delete?')) return; await api(`/admin/api/nav.php?action=delete_mega_subitem&id=${id}`,{method:'POST'}); cat.subitems=cat.subitems.filter(s=>s.id!==id); };

        /* ─── Solutions ─────────────────────────────────────────────────── */
        const solutionsTab = ref('solution');
        const solutionItems = ref([]);
        const solutionItemForm = reactive({id:0,type:'solution',title:'',description:'',btn_text:'Learn more',btn_url:'#',icon_svg:'',sort_order:0,is_active:1});

        const loadSolutionItems = async () => {
            solutionItems.value = await api(`/admin/api/solutions.php?action=items&type=${solutionsTab.value}&lang_id=${langId.value}`);
        };
        const openSolutionItemModal = (item=null) => { Object.assign(solutionItemForm,item?{...item}:{id:0,type:solutionsTab.value,title:'',description:'',btn_text:'Learn more',btn_url:'#',icon_svg:'',sort_order:solutionItems.value.length,is_active:1}); modals.solutionItem=true; };
        const saveSolutionItem = async () => {
            await api(`/admin/api/solutions.php?action=save_item&lang_id=${langId.value}${solutionItemForm.id?'&id='+solutionItemForm.id:''}`,{method:'POST',body:JSON.stringify(solutionItemForm)});
            modals.solutionItem=false; await loadSolutionItems(); showAlert('Saved!');
        };
        const deleteSolutionItem = async id => { if(!confirm('Delete?')) return; await api(`/admin/api/solutions.php?action=delete_item&id=${id}`,{method:'POST'}); await loadSolutionItems(); };

        /* ─── Cases ─────────────────────────────────────────────────────── */
        const casesList = ref([]);
        const editingCase = ref(null);
        const caseTab = ref('Basic');
        const allTerms = ref([]);
        const caseForm = reactive({id:0,slug:'',title:'',description:'',overview_text:'',location:'',cooperation_period:'',key_results_text:'',services_text:'',meta_title:'',meta_description:'',is_active:1,is_featured:0,sort_order:0,featured_image_id:null,company_logo_id:null,image_url:'',logo_url:'',challenges:[],tech_items:[],term_ids:[]});

        const loadCases = async () => { casesList.value = await api(`/admin/api/cases.php?lang_id=${langId.value}`); };
        const openCaseEditor = async (c=null) => {
            caseTab.value='Basic';
            if (c) {
                const full = await api(`/admin/api/cases.php?id=${c.id}&lang_id=${langId.value}`);
                Object.assign(caseForm,{...full,key_results_text:(full.key_results||[]).map(r=>r.text).join('\n'),services_text:(full.services||[]).map(s=>s.service_name).join('\n'),challenges:full.challenges.map(ch=>({id:ch.id,title:ch.ch_title||'',text:ch.ch_text||''})),tech_items:full.tech_items||[],term_ids:full.term_ids||[]});
            } else {
                Object.assign(caseForm,{id:0,slug:'',title:'',description:'',overview_text:'',location:'',cooperation_period:'',key_results_text:'',services_text:'',meta_title:'',meta_description:'',is_active:1,is_featured:0,sort_order:0,featured_image_id:null,company_logo_id:null,image_url:'',logo_url:'',challenges:[],tech_items:[],term_ids:[]});
            }
            editingCase.value = true;
        };
        const saveCase = async () => {
            const payload = {...caseForm,
                key_results: caseForm.key_results_text.split('\n').filter(l=>l.trim()),
                services: caseForm.services_text.split('\n').filter(l=>l.trim()),
            };
            const r = await api(`/admin/api/cases.php?action=save&lang_id=${langId.value}${caseForm.id?'&id='+caseForm.id:''}`,{method:'POST',body:JSON.stringify(payload)});
            if (r.ok) { caseForm.id = r.id; showAlert('Case saved!'); }
            else showAlert(r.error||'Error','error');
        };
        const deleteCase = async id => { if(!confirm('Delete this case?')) return; await api(`/admin/api/cases.php?action=delete&id=${id}`,{method:'POST'}); await loadCases(); };

        /* ─── Blog ──────────────────────────────────────────────────────── */
        const postsList = ref([]);
        const editingPost = ref(null);
        const postTab = ref('Basic');
        const authorsList = ref([]);
        const postForm = reactive({id:0,slug:'',title:'',subtitle:'',content:'',excerpt:'',meta_title:'',meta_description:'',is_active:1,featured_image_id:null,image_url:'',author_id:null,published_at:'',tags_text:'',tags:[],toc:[]});

        const loadPosts = async () => { postsList.value = await api(`/admin/api/posts.php?lang_id=${langId.value}`); };
        const loadAuthors = async () => { authorsList.value = await api(`/admin/api/authors.php?lang_id=${langId.value}`); };
        const openPostEditor = async (p=null) => {
            postTab.value='Basic'; destroyTinyMCE();
            if (p) {
                const full = await api(`/admin/api/posts.php?id=${p.id}&lang_id=${langId.value}`);
                Object.assign(postForm,{...full,tags_text:(full.tags||[]).map(t=>t.tag_text).join('\n'),toc:full.toc||[]});
            } else {
                Object.assign(postForm,{id:0,slug:'',title:'',subtitle:'',content:'',excerpt:'',meta_title:'',meta_description:'',is_active:1,featured_image_id:null,image_url:'',author_id:null,published_at:new Date().toISOString().slice(0,16),tags_text:'',tags:[],toc:[]});
            }
            editingPost.value = true;
            if (!authorsList.value.length) await loadAuthors();
        };
        const switchPostTab = async t => {
            if (t==='Content') {
                postTab.value=t;
                await nextTick();
                initTinyMCE();
            } else {
                if (tinymce.get('tinymce-editor')) postForm.content = tinymce.get('tinymce-editor').getContent();
                destroyTinyMCE();
                postTab.value=t;
            }
        };
        const initTinyMCE = () => {
            if (tinymce.get('tinymce-editor')) return;
            tinymce.init({selector:'#tinymce-editor',height:500,plugins:'lists link image code table',toolbar:'undo redo | bold italic underline | bullist numlist | link image | h2 h3 h4 | code',setup(ed){ed.on('change',()=>{postForm.content=ed.getContent();})}});
            if (postForm.content) { setTimeout(()=>{ const ed=tinymce.get('tinymce-editor'); if(ed) ed.setContent(postForm.content); },500); }
        };
        const destroyTinyMCE = () => { const ed=tinymce.get('tinymce-editor'); if(ed) { postForm.content=ed.getContent(); ed.remove(); } };
        const savePost = async () => {
            if (tinymce.get('tinymce-editor')) postForm.content = tinymce.get('tinymce-editor').getContent();
            const payload = {...postForm, tags:postForm.tags_text.split('\n').filter(l=>l.trim())};
            const r = await api(`/admin/api/posts.php?action=save&lang_id=${langId.value}${postForm.id?'&id='+postForm.id:''}`,{method:'POST',body:JSON.stringify(payload)});
            if (r.ok) { postForm.id=r.id; showAlert('Post saved!'); }
            else showAlert(r.error||'Error','error');
        };
        const deletePost = async id => { if(!confirm('Delete this post?')) return; await api(`/admin/api/posts.php?action=delete&id=${id}`,{method:'POST'}); await loadPosts(); };

        /* ─── Terms ─────────────────────────────────────────────────────── */
        const termForm = reactive({id:0,type:'solution',name:'',slug:'',sort_order:0});
        const loadTerms = async () => { allTerms.value = await api(`/admin/api/terms.php?lang_id=${langId.value}`); };
        const openTermModal = (t=null) => { Object.assign(termForm,t?{...t}:{id:0,type:'solution',name:'',slug:'',sort_order:allTerms.value.length}); modals.term=true; };
        const saveTerm = async () => {
            if (!termForm.slug) termForm.slug=slugify(termForm.name);
            await api(`/admin/api/terms.php?action=save&lang_id=${langId.value}${termForm.id?'&id='+termForm.id:''}`,{method:'POST',body:JSON.stringify(termForm)});
            modals.term=false; await loadTerms(); showAlert('Saved!');
        };
        const deleteTerm = async id => { if(!confirm('Delete?')) return; await api(`/admin/api/terms.php?action=delete&id=${id}`,{method:'POST'}); await loadTerms(); };

        /* ─── Authors ───────────────────────────────────────────────────── */
        const authorForm = reactive({id:0,name:'',title:'',linkedin_url:'',image_id:null,image_url:''});
        const openAuthorModal = (a=null) => { Object.assign(authorForm,a?{...a,image_url:a.image_url||''}:{id:0,name:'',title:'',linkedin_url:'',image_id:null,image_url:''}); modals.author=true; };
        const saveAuthor = async () => {
            await api(`/admin/api/authors.php?action=save&lang_id=${langId.value}${authorForm.id?'&id='+authorForm.id:''}`,{method:'POST',body:JSON.stringify(authorForm)});
            modals.author=false; await loadAuthors(); showAlert('Saved!');
        };
        const deleteAuthor = async id => { if(!confirm('Delete?')) return; await api(`/admin/api/authors.php?action=delete&id=${id}`,{method:'POST'}); await loadAuthors(); };

        /* ─── Media ─────────────────────────────────────────────────────── */
        const mediaList = ref([]);
        let mediaPickerCallback = null;

        const loadMedia = async () => {
            const r = await api('/admin/api/media.php');
            mediaList.value = r.items || [];
            stats.media = r.total || 0;
        };
        const uploadFiles = async e => {
            const files = e.target.files;
            for (const file of files) {
                const fd = new FormData();
                fd.append('file', file);
                await fetch('/admin/api/upload.php',{method:'POST',body:fd});
            }
            await loadMedia(); showAlert('Uploaded!');
        };
        const uploadAndPick = async e => {
            const file = e.target.files[0];
            const fd = new FormData(); fd.append('file',file);
            const r = await (await fetch('/admin/api/upload.php',{method:'POST',body:fd})).json();
            if (r.media) { selectMedia({...r.media,url:r.media.url}); }
            await loadMedia();
        };
        const pickMedia = callback => { mediaPickerCallback=callback; modals.mediaPicker=true; if(!mediaList.value.length) loadMedia(); };
        const selectMedia = m => { if(mediaPickerCallback) mediaPickerCallback(m); modals.mediaPicker=false; mediaPickerCallback=null; };
        const deleteMedia = async id => { if(!confirm('Delete this file?')) return; await api(`/admin/api/media.php?action=delete&id=${id}`,{method:'POST',body:'{}'}); await loadMedia(); };
        const copyUrl = url => { navigator.clipboard.writeText(location.origin+url); showAlert('URL copied!'); };

        /* ─── Settings ──────────────────────────────────────────────────── */
        const settingsData = reactive({});
        const loadSettings = async () => {
            const r = await api(`/admin/api/settings.php?lang_id=${langId.value}`);
            Object.assign(settingsData, r);
        };
        const saveSettings = async () => {
            await api(`/admin/api/settings.php?lang_id=${langId.value}`,{method:'POST',body:JSON.stringify(settingsData)});
            showAlert('Settings saved!');
        };

        /* ─── Dashboard Stats ───────────────────────────────────────────── */
        const loadStats = async () => {
            const [c,p,s,m] = await Promise.all([
                api(`/admin/api/cases.php?lang_id=${langId.value}`),
                api(`/admin/api/posts.php?lang_id=${langId.value}`),
                api(`/admin/api/solutions.php?action=items&lang_id=${langId.value}`),
                api('/admin/api/media.php'),
            ]);
            stats.cases = Array.isArray(c)?c.length:0;
            stats.posts = Array.isArray(p)?p.length:0;
            stats.solutions = Array.isArray(s)?s.length:0;
            stats.media = m.total||0;
        };

        /* ─── Lang Change ───────────────────────────────────────────────── */
        const onLangChange = () => {
            const v = currentView.value;
            if (v==='home') loadHome();
            else if (v==='cases') { editingCase.value=null; loadCases(); }
            else if (v==='blog') { editingPost.value=null; loadPosts(); }
            else if (v==='nav') loadNav();
            else if (v==='solutions') loadSolutionItems();
            else if (v==='terms') loadTerms();
            else if (v==='settings') loadSettings();
        };

        /* ─── View change watcher ───────────────────────────────────────── */
        const onViewChange = v => {
            if (v==='home') loadHome();
            else if (v==='cases') { editingCase.value=null; loadCases(); loadTerms(); }
            else if (v==='blog') { editingPost.value=null; loadPosts(); loadAuthors(); }
            else if (v==='nav') loadNav();
            else if (v==='solutions') loadSolutionItems();
            else if (v==='terms') loadTerms();
            else if (v==='authors') loadAuthors();
            else if (v==='media') loadMedia();
            else if (v==='languages') loadLanguages();
            else if (v==='settings') loadSettings();
            else if (v==='dashboard') loadStats();
        };

        /* ─── Init ──────────────────────────────────────────────────────── */
        onMounted(async () => {
            await loadLanguages();
            await loadStats();
        });

        // Watch currentView
        const watchView = () => {
            let prev = currentView.value;
            setInterval(() => { if (currentView.value !== prev) { prev=currentView.value; onViewChange(currentView.value); } }, 50);
        };
        watchView();

        const logout = async () => { await api('/admin/api/auth.php?action=logout',{method:'POST'}); location.href='/admin/login.php'; };

        return {
            adminName, currentView, currentViewLabel, langId, languages, langList, alert, stats,
            navItems, currentLangCode,
            modals, langForm, openLangModal, saveLang, deleteLang, setDefaultLang,
            homeTab, homeData, whySlides, partnerLogos, autoImages, reviews,
            whySlideForm, reviewForm,
            saveHome, openWhySlideModal, saveWhySlide, deleteWhySlide,
            openReviewModal, saveReview, deleteReview,
            addPartnerLogo, deletePartnerLogo, addAutoImage, deleteAutoImage,
            navLocation, navItems_data, navForm, megaNavItem, megaCategories,
            loadNav, openNavModal, saveNavItem, deleteNavItem,
            openMegaEditor, saveMegaCategory, deleteMegaCategory, saveMegaSubitem, deleteMegaSubitem,
            solutionsTab, solutionItems, solutionItemForm,
            loadSolutionItems, openSolutionItemModal, saveSolutionItem, deleteSolutionItem,
            casesList, editingCase, caseTab, allTerms, caseForm,
            openCaseEditor, saveCase, deleteCase,
            postsList, editingPost, postTab, authorsList, postForm,
            openPostEditor, switchPostTab, savePost, deletePost,
            termForm, openTermModal, saveTerm, deleteTerm,
            authorForm, openAuthorModal, saveAuthor, deleteAuthor,
            mediaList, uploadFiles, uploadAndPick, pickMedia, selectMedia, deleteMedia, copyUrl,
            settingsData, saveSettings,
            onLangChange, slugify,
        };
    }
}).mount('#app');
</script>
</body>
</html>
