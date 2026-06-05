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
<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.css">
<script src="https://cdn.ckeditor.com/ckeditor5/43.3.1/ckeditor5.umd.js"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0}
:root{--sidebar:#1a1a2e;--sidebar-hover:#252545;--accent:#772885;--accent2:#8e35a0;--bg:#f0f2f5;--card:#fff;--text:#1a1a1a;--muted:#666;--border:#e2e4e8;--red:#e53935;--green:#43a047}
body{font-family:system-ui,-apple-system,sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex}
#app{display:flex;flex:1;width:100%;min-height:100vh}
a{color:inherit;text-decoration:none}
.sidebar{width:240px;background:var(--sidebar);min-height:100vh;display:flex;flex-direction:column;position:fixed;top:0;left:0;bottom:0;z-index:100}
.sidebar__logo{padding:20px 20px 18px;border-bottom:1px solid rgba(255,255,255,.1)}
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
.ck-editor__editable{min-height:460px!important;font-size:15px;font-family:system-ui,sans-serif;overflow-wrap:break-word;word-break:break-word}
.ck.ck-editor{border-radius:8px!important;border:1px solid var(--border)!important}
/* Fix: global * reset removes list padding — restore it inside CKEditor */
.ck-editor__editable ul,.ck-editor__editable ol{padding-left:24px;margin:8px 0}
.ck-editor__editable li{display:list-item}
.ck-editor__editable ul>li{list-style:disc}
.ck-editor__editable ol>li{list-style:decimal}
.ck-editor__editable ul ul>li,.ck-editor__editable ol ul>li{list-style:circle}
.ck-editor__editable ul ol>li,.ck-editor__editable ol ol>li{list-style:lower-alpha}
.ck.ck-toolbar{border-radius:8px 8px 0 0!important;background:#f8f9fa!important;border-bottom:1px solid var(--border)!important}
.ck-source-editing-area textarea{min-height:460px!important;font-family:monospace;font-size:13px}
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
.repeat-item{background:#f8f9fa;border:1px solid var(--border);border-radius:8px;overflow:hidden}
.repeat-item__header{display:flex;align-items:center;justify-content:space-between;padding:8px 12px;border-bottom:1px solid var(--border);background:#f0f1f3;min-height:36px}
.repeat-item__header-label{font-size:12px;font-weight:600;color:var(--muted)}
.repeat-item__actions{display:flex;gap:6px}
.repeat-item__body{padding:12px 14px}
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
/* SVG field */
.svg-field-row{display:flex;align-items:flex-start;gap:10px}
.svg-preview{width:48px;height:48px;flex-shrink:0;border:1px solid var(--border);border-radius:6px;display:flex;align-items:center;justify-content:center;background:#fff;overflow:hidden}
.svg-preview svg{width:32px;height:32px}
.svg-field-row textarea{flex:1}
/* Drag handle */
.drag-handle{cursor:grab;color:#ccc;font-size:20px;padding:0 4px}
.drag-handle:active{cursor:grabbing}
</style>
</head>
<body>
<div id="app">

<!-- ══ SIDEBAR ══ -->
<aside class="sidebar">
    <div class="sidebar__logo">
        <img src="/assets/img/logo.svg" alt="SIDIS GROUP" style="height:30px;max-width:160px;display:block">
        <div style="font-size:11px;color:rgba(255,255,255,.4);margin-top:6px;letter-spacing:.5px">Admin Panel</div>
    </div>
    <nav class="sidebar__nav">
        <div v-for="item in navItems.filter(n=>!n.adminOnly||isAdmin)" :key="item.id"
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
                <button v-for="t in ['Hero','Why Slides','Automation','Partner Logos','Reviews','Solutions Section','Roadmap & CTA','Presentation','Page Blocks']" :key="t"
                    class="tab-btn" :class="{active:homeTab===t}" @click="homeTab=t;if(t==='Page Blocks')loadBlocks()">{{ t }}</button>
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
                    <div class="field field--full">
                        <label>Hero Poster Image <span style="font-weight:normal;color:var(--muted)">(показывается пока грузится видео)</span></label>
                        <div class="img-picker">
                            <img v-if="homeData.hero_poster_url" :src="homeData.hero_poster_url" class="img-thumb" style="max-height:80px;object-fit:cover">
                            <button class="btn btn--outline btn--sm" @click="pickMedia(m=>{homeData.hero_poster_url=m.url})">Choose from library</button>
                            <button v-if="homeData.hero_poster_url" class="btn btn--danger btn--sm" @click="homeData.hero_poster_url=''">Remove</button>
                        </div>
                    </div>
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
                <div class="section-head" style="margin-bottom:4px">
                    <strong style="font-size:15px;font-weight:600">Partner / Client Logos (Marquee)</strong>
                    <button class="btn btn--primary btn--sm" @click="pickMedia(img=>addPartnerLogo(img))">+ Add Logo</button>
                </div>
                <p style="font-size:12px;color:var(--muted);margin-bottom:12px">Эти логотипы отображаются во всех каруселях на сайте — на главной и на страницах Solutions/Departments/Industries.</p>
                <div class="card"><div class="card__body">
                    <p style="font-size:11px;color:var(--muted);margin-bottom:10px">Перетащите логотипы чтобы изменить порядок</p>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:center" @dragover.prevent @drop.prevent>
                        <div v-for="(logo,i) in partnerLogos" :key="logo.id"
                             draggable="true"
                             @dragstart="onLogoDragStart(i)"
                             @dragover.prevent="onLogoDragOver($event,i)"
                             @drop.prevent="onLogoDrop()"
                             :style="{position:'relative',textAlign:'center',cursor:'grab',opacity:dragLogoIdx===i?0.4:1,transition:'opacity 0.15s'}">
                            <div style="position:absolute;top:-4px;left:50%;transform:translateX(-50%);font-size:10px;color:var(--muted)">⠿</div>
                            <img :src="logo.image_url||''" style="height:50px;max-width:120px;object-fit:contain;border:1px solid var(--border);border-radius:6px;padding:4px;pointer-events:none">
                            <button class="btn btn--danger btn--sm" style="position:absolute;top:-6px;right:-6px;padding:1px 6px;font-size:11px" @click.stop="deletePartnerLogo(logo.id)">×</button>
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
                    <div class="field"><label>Solutions Section Text</label><textarea v-model="homeData.solutions_text" rows="3"></textarea></div>
                    <div class="field"><label>Projects Section Title</label><input v-model="homeData.projects_title"></div>
                    <div class="field"><label>Projects Button Text</label><input v-model="homeData.projects_btn_text"></div>
                    <div class="field"><label>Projects Button URL</label><input v-model="homeData.projects_btn_url"></div>
                    <div class="field"><label>Reviews Section Title</label><input v-model="homeData.reviews_title"></div>
                    <div class="field"><label>Why Section Title</label><input v-model="homeData.why_title"></div>
                    <div class="field"><label>Why Section Subtitle</label><input v-model="homeData.why_subtitle"></div>
                    <div class="field"><label>Why Section Text</label><textarea v-model="homeData.why_text" rows="3"></textarea></div>
                </div>

                <hr style="margin:24px 0;border:none;border-top:1px solid var(--border)">
                <p style="font-size:15px;font-weight:600;margin-bottom:4px">Items shown in Automation Solutions block</p>
                <p style="font-size:12px;color:var(--muted);margin-bottom:16px">Leave all unchecked = show all active items</p>

                <div v-for="group in [{key:'solutions_ids',label:'Solutions',type:'solution'},{key:'departments_ids',label:'Departments',type:'department'},{key:'industries_ids',label:'Industries',type:'industry'}]" :key="group.key" style="margin-bottom:20px">
                    <p style="font-size:13px;font-weight:600;margin-bottom:8px">{{ group.label }}</p>
                    <div style="display:flex;flex-wrap:wrap;gap:8px">
                        <label v-for="p in allSolPages.filter(x=>x.type===group.type)" :key="p.id"
                               style="display:flex;align-items:center;gap:6px;cursor:pointer;padding:4px 10px;background:#f8f9fa;border-radius:6px;font-size:13px">
                            <input type="checkbox" :value="p.id" v-model="homeData[group.key]">
                            {{ p.title || p.slug }}
                        </label>
                        <span v-if="!allSolPages.filter(x=>x.type===group.type).length" style="font-size:12px;color:var(--muted)">No items yet</span>
                    </div>
                </div>
            </div></div>

            <!-- Roadmap & CTA -->
            <div v-if="homeTab==='Roadmap & CTA'" class="card"><div class="card__body">
                <p style="font-size:13px;color:var(--muted);margin-bottom:16px">Roadmap section settings</p>
                <div class="form-grid">
                    <div class="field"><label>Roadmap Title</label><input v-model="homeData.roadmap_title"></div>
                    <div class="field"><label>Roadmap Btn 1 Text</label><input v-model="homeData.roadmap_btn1_text"></div>
                    <div class="field"><label>Roadmap Btn 1 URL</label><input v-model="homeData.roadmap_btn1_url"></div>
                    <div class="field"><label>Roadmap Btn 2 Text</label><input v-model="homeData.roadmap_btn2_text"></div>
                    <div class="field"><label>Roadmap Btn 2 URL</label><input v-model="homeData.roadmap_btn2_url"></div>
                </div>
                <hr style="margin:20px 0;border:none;border-top:1px solid var(--border)">
                <p style="font-size:13px;font-weight:600;margin-bottom:12px">Contact Form Section</p>
                <div class="form-grid cols-1">
                    <div class="field"><label>Section Title</label><input v-model="homeData.cta_title"></div>
                    <div class="field"><label>Form Title</label><input v-model="homeData.cta_form_title"></div>
                    <div class="field"><label>Intro Text (item 1)</label><textarea v-model="homeData.cta_text" rows="3"></textarea></div>
                    <div class="field"><label>Item 2</label><input v-model="homeData.cta_item2"></div>
                    <div class="field"><label>Item 3</label><input v-model="homeData.cta_item3"></div>
                    <div class="field"><label>Submit Button Text</label><input v-model="homeData.cta_btn_text"></div>
                </div>
                <hr style="margin:20px 0;border:none;border-top:1px solid var(--border)">
                <p style="font-size:13px;font-weight:600;margin-bottom:12px">Roadmap Background Video</p>
                <div class="form-grid">
                    <div class="field field--full">
                        <label>Video Path</label>
                        <div style="display:flex;gap:8px;align-items:center">
                            <input v-model="homeData.roadmap_video" placeholder="./assets/video/1-hero.mp4" style="flex:1">
                            <button class="btn btn--outline btn--sm" @click="pickMedia(m=>{homeData.roadmap_video=m.url})">Pick from library</button>
                        </div>
                    </div>
                    <div class="field field--full">
                        <label>Poster Image <span style="font-weight:normal;color:var(--muted)">(обложка до загрузки видео)</span></label>
                        <div class="img-picker">
                            <img v-if="homeData.roadmap_poster_url" :src="homeData.roadmap_poster_url" class="img-thumb" style="max-height:80px;object-fit:cover">
                            <button class="btn btn--outline btn--sm" @click="pickMedia(m=>{homeData.roadmap_poster_url=m.url})">Choose from library</button>
                            <button v-if="homeData.roadmap_poster_url" class="btn btn--danger btn--sm" @click="homeData.roadmap_poster_url=''">Remove</button>
                        </div>
                    </div>
                </div>
            </div></div>

            <!-- Presentation -->
            <div v-if="homeTab==='Presentation'" class="card"><div class="card__body">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                    <p style="font-size:15px;font-weight:600;margin:0">Video Presentation Section</p>
                    <label style="display:flex;align-items:center;gap:8px;cursor:pointer;font-size:13px">
                        <input type="checkbox" v-model="homeData.presentation_enabled" :true-value="1" :false-value="0" style="width:16px;height:16px;accent-color:var(--accent)">
                        <span>Show on homepage</span>
                    </label>
                </div>
                <div class="form-grid">
                    <div class="field"><label>Subtitle (small text above title)</label><input v-model="homeData.presentation_subtitle" placeholder="Bring Ideas To Life"></div>
                    <div class="field"><label>Play Button Text</label><input v-model="homeData.presentation_play_text" placeholder="Play"></div>
                    <div class="field field--full"><label>Title</label><input v-model="homeData.presentation_title" placeholder="See RPA\nin Action"></div>
                    <div class="field field--full"><label>Description text (bottom-right corner)</label><textarea v-model="homeData.presentation_text" rows="3"></textarea></div>
                    <div class="field field--full">
                        <label>Video</label>
                        <div style="display:flex;gap:8px;align-items:center">
                            <input v-model="homeData.presentation_video" placeholder="./assets/video/1-hero.mp4" style="flex:1">
                            <button class="btn btn--outline btn--sm" @click="pickMedia(m=>{homeData.presentation_video=m.url})">Pick from library</button>
                        </div>
                    </div>
                    <div class="field field--full">
                        <label>Cover / Poster Image <span style="font-weight:normal;color:var(--muted)">(фон до нажатия Play)</span></label>
                        <div class="img-picker">
                            <img v-if="homeData.presentation_poster_url" :src="homeData.presentation_poster_url" class="img-thumb" style="max-height:80px;object-fit:cover">
                            <button class="btn btn--outline btn--sm" @click="pickMedia(m=>{homeData.presentation_poster_url=m.url})">Choose from library</button>
                            <button v-if="homeData.presentation_poster_url" class="btn btn--danger btn--sm" @click="homeData.presentation_poster_url=''">Remove</button>
                        </div>
                    </div>
                </div>
            </div></div>

            <!-- Page Blocks Order -->
            <div v-if="homeTab==='Page Blocks'">
                <div class="card" style="margin-bottom:16px">
                    <div class="card__head"><h2>Case Studies to show in Projects section</h2></div>
                    <div class="card__body">
                        <p style="font-size:13px;color:var(--muted);margin-bottom:12px">Select specific cases to display. Leave all unchecked to show featured cases automatically.</p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;max-height:260px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;padding:10px">
                            <label v-for="c in allCases" :key="c.id" style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
                                <input type="checkbox" :value="c.id" v-model="homeData.featured_cases_ids">
                                <span>{{ c.title }}</span>
                            </label>
                            <div v-if="!allCases.length" style="color:var(--muted);font-size:12px">No cases yet</div>
                        </div>
                        <button class="btn btn--primary btn--sm" style="margin-top:10px" @click="saveHome">Save</button>
                    </div>
                </div>
                <div class="card">
                    <div class="card__head"><h2>Homepage Block Order</h2></div>
                    <div class="card__body">
                        <p style="font-size:13px;color:var(--muted);margin-bottom:16px">Drag to reorder blocks on the homepage. Toggle to show/hide sections.</p>
                        <div class="repeat-list">
                            <div v-for="(block,i) in homeBlocksList" :key="block.id" class="repeat-item" style="display:flex;align-items:center;gap:16px;cursor:grab">
                                <span style="font-size:18px;color:#ccc;cursor:grab" title="Drag to reorder">⠿</span>
                                <div style="flex:1;font-weight:500">{{ block.label }}</div>
                                <div style="font-size:12px;color:var(--muted);font-family:monospace">{{ block.block_key }}</div>
                                <label class="toggle">
                                    <input type="checkbox" :checked="block.is_active==1" @change="toggleBlock(block)">
                                    <span class="toggle__slider"></span>
                                </label>
                                <div style="display:flex;gap:4px">
                                    <button class="btn btn--outline btn--sm" :disabled="i===0" @click="moveBlock(i,-1)">↑</button>
                                    <button class="btn btn--outline btn--sm" :disabled="i===homeBlocksList.length-1" @click="moveBlock(i,1)">↓</button>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn--primary" style="margin-top:16px" @click="saveBlocksOrder">Save Block Order</button>
                    </div>
                </div>
            </div>
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
            <div class="section-head">
                <h1>Solutions / Departments / Industries</h1>
            </div>

            <!-- ── SOLUTION PAGES EDITOR ──────────────────────────────────── -->
            <div style="margin-top:12px">

                <!-- Page list -->
                <div v-if="!editingSolPage && !editingSolBlocks">
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
                        <p style="font-size:13px;color:var(--muted)">Each row is a dedicated page at /solutions/slug, /departments/slug or /industries/slug. Click Edit to manage all content blocks.</p>
                        <button class="btn btn--primary" @click="openSolPageEditor()">+ New Page</button>
                    </div>
                    <div class="tabs" style="margin-bottom:12px">
                        <button v-for="t in ['solution','department','industry']" :key="t" class="tab-btn"
                            :class="{active:solPagesTab===t}" @click="solPagesTab=t">
                            {{ t.charAt(0).toUpperCase()+t.slice(1)+'s' }}
                        </button>
                    </div>
                    <div class="card"><div class="table-wrap"><table>
                        <thead><tr><th>Title</th><th>Slug</th><th>Active</th><th>Sort</th><th>Actions</th></tr></thead>
                        <tbody>
                            <tr v-for="p in solPagesList.filter(x=>x.type===solPagesTab)" :key="p.id">
                                <td style="font-weight:500">{{ p.title || '—' }}</td>
                                <td style="font-size:12px;color:var(--muted)">{{ p.slug }}</td>
                                <td><span :class="'badge badge--'+(p.is_active?'on':'off')">{{ p.is_active?'Yes':'No' }}</span></td>
                                <td>{{ p.sort_order }}</td>
                                <td>
                                    <button class="btn btn--outline btn--sm" @click="openSolPageEditor(p)">⚙ Settings</button>
                                    <button class="btn btn--primary btn--sm" style="margin-left:4px" @click="openSolBlockEditor(p)">✏ Edit Blocks</button>
                                    <button class="btn btn--danger btn--sm" style="margin-left:4px" @click="deleteSolPage(p)">Del</button>
                                </td>
                            </tr>
                            <tr v-if="!solPagesList.filter(x=>x.type===solPagesTab).length">
                                <td colspan="5" class="empty" style="padding:20px;text-align:center;color:var(--muted)">No pages yet. Click "+ New Page"</td>
                            </tr>
                        </tbody>
                    </table></div></div>
                </div>

                <!-- Page settings editor -->
                <div v-if="editingSolPage && !editingSolBlocks">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
                        <button class="btn btn--outline" @click="editingSolPage=false">← Back</button>
                        <h2 style="font-size:18px;font-weight:600">{{ solPageForm.id ? 'Edit' : 'New' }} Page Settings</h2>
                        <a v-if="solPageForm.slug" :href="'/'+(solPageForm.type==='industry'?'industries':solPageForm.type+'s')+'/'+solPageForm.slug+'/'" target="_blank" class="btn btn--outline btn--sm" style="margin-left:auto">↗ View Page</a>
                        <button class="btn btn--primary" @click="saveSolPage">Save</button>
                    </div>
                    <div class="card"><div class="card__body">
                        <div class="form-grid">
                            <div class="field"><label>Type</label>
                                <select v-model="solPageForm.type">
                                    <option value="solution">Solution</option>
                                    <option value="department">Department</option>
                                    <option value="industry">Industry</option>
                                </select>
                            </div>
                            <div class="field"><label>Slug (URL)</label>
                                <input v-model="solPageForm.slug" @input="solPageForm.slug=slugify(solPageForm.slug)">
                                <div class="slug-preview">/{{ solPageForm.type === 'industry' ? 'industries' : solPageForm.type + 's' }}/{{ solPageForm.slug }}/</div>
                            </div>
                            <div class="field field--full"><label>Title</label><input v-model="solPageForm.title"></div>
                            <div class="field field--full"><label>Description</label><textarea v-model="solPageForm.description" rows="3"></textarea></div>
                            <div class="field"><label>Button 1 Text</label><input v-model="solPageForm.btn1_text"></div>
                            <div class="field"><label>Button 2 Text</label><input v-model="solPageForm.btn2_text"></div>
                            <div class="field field--full"><label>Icon SVG (black — for homepage tabs)</label>
                                <div class="svg-field-row">
                                    <div v-if="solPageForm.icon_svg" v-html="solPageForm.icon_svg" style="width:44px;height:44px;flex-shrink:0;overflow:hidden"></div>
                                    <textarea v-model="solPageForm.icon_svg" rows="3" placeholder="Paste SVG code or upload file"></textarea>
                                    <label class="btn btn--outline btn--sm" style="cursor:pointer;white-space:nowrap">
                                        Upload SVG<input type="file" accept=".svg,image/svg+xml" style="display:none" @change="loadSvgInto($event,solPageForm,'icon_svg')">
                                    </label>
                                </div>
                            </div>
                            <div class="field field--full"><label>Icon SVG White (for hover / active states)</label>
                                <div class="svg-field-row">
                                    <div v-if="solPageForm.icon_svg_white" v-html="solPageForm.icon_svg_white" style="width:44px;height:44px;flex-shrink:0;overflow:hidden;background:#222;border-radius:6px;padding:4px"></div>
                                    <textarea v-model="solPageForm.icon_svg_white" rows="3" placeholder="Paste white SVG code or upload file"></textarea>
                                    <label class="btn btn--outline btn--sm" style="cursor:pointer;white-space:nowrap">
                                        Upload SVG<input type="file" accept=".svg,image/svg+xml" style="display:none" @change="loadSvgInto($event,solPageForm,'icon_svg_white')">
                                    </label>
                                </div>
                            </div>
                            <div class="field"><label>Featured Image</label>
                                <div class="img-picker">
                                    <img v-if="solPageForm.image_url" :src="solPageForm.image_url" class="img-thumb">
                                    <button class="btn btn--outline btn--sm" @click="pickMedia(m=>{solPageForm.image_id=m.id;solPageForm.image_url=m.url})">Choose</button>
                                    <button v-if="solPageForm.image_url" class="btn btn--sm" @click="solPageForm.image_id=null;solPageForm.image_url=''">Clear</button>
                                </div>
                            </div>
                            <div class="field"><label>Sort Order</label><input type="number" v-model.number="solPageForm.sort_order"></div>
                            <div class="field" style="flex-direction:row;align-items:center;gap:10px;padding-top:22px">
                                <label class="toggle"><input type="checkbox" v-model="solPageForm.is_active" :true-value="1" :false-value="0"><span class="toggle__slider"></span></label>
                                <span>Active</span>
                            </div>
                            <div class="field field--full"><label>Meta Title</label><input v-model="solPageForm.meta_title"></div>
                            <div class="field field--full"><label>Meta Description</label><textarea v-model="solPageForm.meta_description" rows="2"></textarea></div>
                        </div>
                    </div></div>
                </div>

                <!-- Block content editor -->
                <div v-if="editingSolBlocks">
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:20px">
                        <button class="btn btn--outline" @click="editingSolBlocks=false;editingSolPage=false">← Back to Pages</button>
                        <h2 style="font-size:18px;font-weight:600">Blocks: {{ editingSolPageObj?.title }}</h2>
                        <a :href="'/'+(editingSolPageObj?.type==='industry'?'industries':(editingSolPageObj?.type||'')+'s')+'/'+editingSolPageObj?.slug+'/' " target="_blank" class="btn btn--outline btn--sm" style="margin-left:auto">↗ View Page</a>
                    </div>

                    <div v-for="(blk,bi) in solPageBlocks" :key="blk.id" class="card" style="margin-bottom:12px">
                        <div class="card__head" style="cursor:pointer" @click="blk._open=!blk._open">
                            <div style="display:flex;align-items:center;gap:12px;flex:1">
                                <span class="drag-handle">⠿</span>
                                <strong style="font-size:14px">{{ blk.label }}</strong>
                                <span style="font-size:11px;color:var(--muted);font-family:monospace">{{ blk.block_key }}</span>
                                <span :class="'badge badge--'+(blk.is_active?'on':'off')" style="margin-left:8px">{{ blk.is_active?'Shown':'Hidden' }}</span>
                            </div>
                            <div style="display:flex;gap:6px;align-items:center">
                                <label class="toggle" @click.stop="">
                                    <input type="checkbox" :checked="blk.is_active==1" @change="blk.is_active=blk.is_active?0:1;saveSolBlock(blk)">
                                    <span class="toggle__slider"></span>
                                </label>
                                <button class="btn btn--outline btn--sm" :disabled="bi===0" @click.stop="solPageBlocks.splice(bi-1,0,...solPageBlocks.splice(bi,1));reorderSolBlocks()">↑</button>
                                <button class="btn btn--outline btn--sm" :disabled="bi===solPageBlocks.length-1" @click.stop="solPageBlocks.splice(bi+1,0,...solPageBlocks.splice(bi,1));reorderSolBlocks()">↓</button>
                            </div>
                        </div>
                        <div v-show="blk._open" class="card__body">

                            <!-- PROMO block -->
                            <div v-if="blk.block_key==='promo'" class="form-grid">
                                <div class="field field--full"><label>Title</label><input v-model="blk.content_obj.title"></div>
                                <div class="field field--full"><label>Text / Description</label><textarea v-model="blk.content_obj.text" rows="3"></textarea></div>
                                <div class="field"><label>Button 1 Text</label><input v-model="blk.content_obj.btn1_text"></div>
                                <div class="field"><label>Button 1 URL</label><input v-model="blk.content_obj.btn1_url"></div>
                                <div class="field"><label>Button 2 Text</label><input v-model="blk.content_obj.btn2_text"></div>
                                <div class="field"><label>Button 2 URL</label><input v-model="blk.content_obj.btn2_url"></div>
                                <div class="field field--full"><label>Image</label>
                                    <div class="img-picker">
                                        <img v-if="blk.content_obj.image_url" :src="blk.content_obj.image_url" class="img-thumb">
                                        <button class="btn btn--outline btn--sm" @click="pickMedia(m=>{blk.content_obj.image_id=m.id;blk.content_obj.image_url=m.url})">Choose Image</button>
                                    </div>
                                </div>
                            </div>

                            <!-- FEATURES block -->
                            <div v-else-if="blk.block_key==='features'">
                                <div class="field" style="margin-bottom:16px"><label>Section Title</label><input v-model="blk.content_obj.title"></div>
                                <div style="display:flex;justify-content:space-between;margin-bottom:8px"><strong style="font-size:13px">Slides</strong>
                                    <button class="btn btn--outline btn--sm" @click="blk.content_obj.slides=blk.content_obj.slides||[];blk.content_obj.slides.push({title:'',text:''})">+ Add Slide</button>
                                </div>
                                <div class="repeat-list">
                                    <div v-for="(sl,si) in (blk.content_obj.slides||[])" :key="si" class="repeat-item">
                                        <div class="repeat-item__header"><span class="repeat-item__header-label">Slide {{ si+1 }}</span>
                                            <div class="repeat-item__actions"><button class="btn btn--danger btn--sm btn--icon" @click="blk.content_obj.slides.splice(si,1)">×</button></div>
                                        </div>
                                        <div class="repeat-item__body form-grid">
                                            <div class="field"><label>Title</label><input v-model="sl.title"></div>
                                            <div class="field"><label>Text</label><textarea v-model="sl.text" rows="2"></textarea></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- PLANNING block -->
                            <div v-else-if="blk.block_key==='planning'">
                                <div style="display:flex;justify-content:space-between;margin-bottom:8px"><strong style="font-size:13px">Text Items</strong>
                                    <button class="btn btn--outline btn--sm" @click="blk.content_obj.items=blk.content_obj.items||[];blk.content_obj.items.push({title:'',text:''})">+ Add</button>
                                </div>
                                <div class="repeat-list">
                                    <div v-for="(it,ii) in (blk.content_obj.items||[])" :key="ii" class="repeat-item">
                                        <div class="repeat-item__header"><span class="repeat-item__header-label">Item {{ ii+1 }}</span>
                                            <div class="repeat-item__actions"><button class="btn btn--danger btn--sm btn--icon" @click="blk.content_obj.items.splice(ii,1)">×</button></div>
                                        </div>
                                        <div class="repeat-item__body form-grid">
                                            <div class="field"><label>Title</label><input v-model="it.title"></div>
                                            <div class="field field--full"><label>Text</label><textarea v-model="it.text" rows="3"></textarea></div>
                                        </div>
                                    </div>
                                </div>
                                <hr style="margin:16px 0;border:none;border-top:1px solid var(--border)">
                                <div class="form-grid cols-1">
                                    <div class="field"><label>Info Box Title</label><input v-model="blk.content_obj.info_title"></div>
                                    <div class="field"><label>Button 1 Text</label><input v-model="blk.content_obj.info_btn1_text" placeholder="View Our Presentation"></div>
                                    <div class="field"><label>Button 1 URL</label><input v-model="blk.content_obj.info_btn1_url" placeholder="#"></div>
                                    <div class="field"><label>Button 2 Text (with arrow icon)</label><input v-model="blk.content_obj.info_btn2_text" placeholder="Free audit"></div>
                                    <div class="field"><label>Button 2 URL</label><input v-model="blk.content_obj.info_btn2_url" placeholder="#getintouch"></div>
                                </div>
                            </div>

                            <!-- SOLVED block -->
                            <div v-else-if="blk.block_key==='solved'">
                                <div class="field" style="margin-bottom:16px"><label>Section Title</label><input v-model="blk.content_obj.title"></div>
                                <div style="display:flex;justify-content:space-between;margin-bottom:8px"><strong style="font-size:13px">Slides</strong>
                                    <button class="btn btn--outline btn--sm" @click="blk.content_obj.slides=blk.content_obj.slides||[];blk.content_obj.slides.push({title:'',text:''})">+ Add</button>
                                </div>
                                <div class="repeat-list">
                                    <div v-for="(sl,si) in (blk.content_obj.slides||[])" :key="si" class="repeat-item">
                                        <div class="repeat-item__header"><span class="repeat-item__header-label">Slide {{ si+1 }}</span>
                                            <div class="repeat-item__actions"><button class="btn btn--danger btn--sm btn--icon" @click="blk.content_obj.slides.splice(si,1)">×</button></div>
                                        </div>
                                        <div class="repeat-item__body form-grid cols-1">
                                            <div class="field"><label>Title</label><input v-model="sl.title"></div>
                                            <div class="field"><label>Text</label><textarea v-model="sl.text" rows="4"></textarea></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ROADMAP block -->
                            <div v-else-if="blk.block_key==='roadmap'">
                                <div class="form-grid" style="margin-bottom:16px">
                                    <div class="field field--full"><label>Section Title</label><input v-model="blk.content_obj.title"></div>
                                    <div class="field"><label>Button 1 Text</label><input v-model="blk.content_obj.btn1_text"></div>
                                    <div class="field"><label>Button 1 URL</label><input v-model="blk.content_obj.btn1_url"></div>
                                    <div class="field"><label>Button 2 Text</label><input v-model="blk.content_obj.btn2_text"></div>
                                    <div class="field"><label>Button 2 URL</label><input v-model="blk.content_obj.btn2_url"></div>
                                    <div class="field field--full"><label>Background Video Path</label><input v-model="blk.content_obj.video_path" placeholder="./assets/video/1-hero.mp4"></div>
                                    <div class="field field--full"><label>Background Video Poster</label><input v-model="blk.content_obj.video_poster" placeholder="./assets/img/poster.webp"></div>
                                </div>
                                <div style="display:flex;justify-content:space-between;margin-bottom:8px"><strong style="font-size:13px">Process Steps</strong>
                                    <button class="btn btn--outline btn--sm" @click="blk.content_obj.steps=blk.content_obj.steps||[];blk.content_obj.steps.push({title:'',text:''})">+ Add Step</button>
                                </div>
                                <div class="repeat-list">
                                    <div v-for="(st,si) in (blk.content_obj.steps||[])" :key="si" class="repeat-item">
                                        <div class="repeat-item__header"><span class="repeat-item__header-label">Step {{ si+1 }}</span>
                                            <div class="repeat-item__actions"><button class="btn btn--danger btn--sm btn--icon" @click="blk.content_obj.steps.splice(si,1)">×</button></div>
                                        </div>
                                        <div class="repeat-item__body form-grid">
                                            <div class="field"><label>Title</label><input v-model="st.title"></div>
                                            <div class="field"><label>Text</label><textarea v-model="st.text" rows="2"></textarea></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- FAQ block -->
                            <div v-else-if="blk.block_key==='faq'">
                                <div class="field" style="margin-bottom:16px"><label>Section Title</label><input v-model="blk.content_obj.title"></div>
                                <div style="display:flex;justify-content:space-between;margin-bottom:8px"><strong style="font-size:13px">Q&A Items</strong>
                                    <button class="btn btn--outline btn--sm" @click="blk.content_obj.items=blk.content_obj.items||[];blk.content_obj.items.push({q:'',a:''})">+ Add</button>
                                </div>
                                <div class="repeat-list">
                                    <div v-for="(it,ii) in (blk.content_obj.items||[])" :key="ii" class="repeat-item">
                                        <div class="repeat-item__header"><span class="repeat-item__header-label">Q {{ ii+1 }}</span>
                                            <div class="repeat-item__actions"><button class="btn btn--danger btn--sm btn--icon" @click="blk.content_obj.items.splice(ii,1)">×</button></div>
                                        </div>
                                        <div class="repeat-item__body form-grid cols-1">
                                            <div class="field"><label>Question</label><input v-model="it.q"></div>
                                            <div class="field"><label>Answer</label><textarea v-model="it.a" rows="3"></textarea></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- AUTO blocks (projects, reviews, articles, getintouch) -->
                            <div v-else>
                                <div v-if="blk.block_key!=='getintouch'" class="field" style="margin-bottom:14px"><label>Section Title</label><input v-model="blk.content_obj.title"></div>
                                <!-- REVIEWS selector -->
                                <div v-if="blk.block_key==='reviews'">
                                    <div style="font-size:13px;font-weight:500;margin-bottom:8px">Select Reviews to show (leave none checked = show all active)</div>
                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;max-height:220px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;padding:10px">
                                        <label v-for="r in allReviews" :key="r.id" style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
                                            <input type="checkbox" :value="r.id" v-model="blk.content_obj.selected_ids">
                                            <span>{{ r.author_name }} — {{ r.quote?.slice(0,40) }}…</span>
                                        </label>
                                        <div v-if="!allReviews.length" style="color:var(--muted);font-size:12px">No reviews yet</div>
                                    </div>
                                </div>
                                <!-- PROJECTS selector -->
                                <div v-if="blk.block_key==='projects'">
                                    <div style="font-size:13px;font-weight:500;margin-bottom:8px">Select Case Studies to show (leave none = show featured)</div>
                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;max-height:220px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;padding:10px">
                                        <label v-for="c in allCases" :key="c.id" style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
                                            <input type="checkbox" :value="c.id" v-model="blk.content_obj.selected_ids">
                                            <span>{{ c.title }}</span>
                                        </label>
                                        <div v-if="!allCases.length" style="color:var(--muted);font-size:12px">No cases yet</div>
                                    </div>
                                </div>
                                <!-- ARTICLES selector -->
                                <div v-if="blk.block_key==='articles'">
                                    <div style="font-size:13px;font-weight:500;margin-bottom:8px">Select Articles to show (leave none = show latest 4)</div>
                                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;max-height:220px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;padding:10px">
                                        <label v-for="p in allPosts" :key="p.id" style="display:flex;align-items:center;gap:8px;font-size:13px;cursor:pointer">
                                            <input type="checkbox" :value="p.id" v-model="blk.content_obj.selected_ids">
                                            <span>{{ p.title }}</span>
                                        </label>
                                        <div v-if="!allPosts.length" style="color:var(--muted);font-size:12px">No posts yet</div>
                                    </div>
                                </div>
                                <p v-if="blk.block_key==='getintouch'" style="font-size:12px;color:var(--muted)">Contact form — uses global settings</p>
                            </div>

                            <div style="margin-top:16px;padding-top:12px;border-top:1px solid var(--border)">
                                <button class="btn btn--primary btn--sm" @click="saveSolBlock(blk)">Save Block</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- ── REVIEWS ── -->
        <template v-if="currentView==='reviews'">
            <div class="section-head">
                <h1>Client Reviews</h1>
                <button class="btn btn--primary" @click="openReviewModal()">+ Add Review</button>
            </div>
            <div class="card"><div class="table-wrap"><table>
                <thead><tr><th>Author</th><th>Position</th><th>Quote (headline)</th><th>Active</th><th>Actions</th></tr></thead>
                <tbody>
                    <tr v-for="r in reviews" :key="r.id">
                        <td style="font-weight:500">{{ r.author_name }}</td>
                        <td style="font-size:12px;color:var(--muted)">{{ r.author_title }}</td>
                        <td style="max-width:300px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ r.quote }}</td>
                        <td><span :class="'badge badge--'+(r.is_active?'on':'off')">{{ r.is_active ? 'Yes' : 'No' }}</span></td>
                        <td>
                            <button class="btn btn--outline btn--sm" @click="openReviewModal(r)">Edit</button>
                            <button class="btn btn--danger btn--sm" style="margin-left:6px" @click="deleteReview(r.id)">Del</button>
                        </td>
                    </tr>
                    <tr v-if="!reviews.length"><td colspan="5" class="empty" style="padding:20px;text-align:center;color:var(--muted)">No reviews yet. Click "+ Add Review"</td></tr>
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
                        <div class="field"><label>Company Name (shown in listings)</label><input v-model="caseForm.company_name" placeholder="e.g. Apex Agency"></div>
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
                        <div class="field field--full">
                            <label>Click Behavior</label>
                            <div style="display:flex;gap:16px;margin-top:6px;flex-wrap:wrap">
                                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:14px"><input type="radio" v-model="caseForm.link_behavior" value="case_page"> Open case page</label>
                                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:14px"><input type="radio" v-model="caseForm.link_behavior" value="external_url"> Open client website</label>
                                <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:14px"><input type="radio" v-model="caseForm.link_behavior" value="inactive"> Non-clickable</label>
                            </div>
                        </div>
                        <div v-if="caseForm.link_behavior==='external_url'" class="field field--full">
                            <label>Client Website URL</label>
                            <input v-model="caseForm.external_url" placeholder="https://...">
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
                                <div class="repeat-item__header">
                                    <span class="repeat-item__header-label">Challenge {{ i+1 }}</span>
                                    <div class="repeat-item__actions"><button class="btn btn--danger btn--sm btn--icon" @click="caseForm.challenges.splice(i,1)">×</button></div>
                                </div>
                                <div class="repeat-item__body">
                                    <div class="form-grid cols-1" style="gap:8px">
                                        <div class="field"><label>Title</label><input v-model="ch.title"></div>
                                        <div class="field"><label>Description</label><textarea v-model="ch.text" rows="3"></textarea></div>
                                    </div>
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
                                <div class="repeat-item__header">
                                    <span class="repeat-item__header-label">{{ ti.name || 'Tech Item '+(i+1) }}</span>
                                    <div class="repeat-item__actions"><button class="btn btn--danger btn--sm btn--icon" @click="caseForm.tech_items.splice(i,1)">×</button></div>
                                </div>
                                <div class="repeat-item__body">
                                    <div class="form-grid">
                                        <div class="field"><label>Name</label><input v-model="ti.name"></div>
                                        <div class="field"><label>Icon SVG</label>
                                            <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                                                <div class="svg-preview" v-html="ti.icon_svg||'<span style=color:#ccc;font-size:10px>No icon</span>'"></div>
                                                <label class="btn btn--outline btn--sm" style="cursor:pointer">Upload SVG<input type="file" accept=".svg,image/svg+xml" style="display:none" @change="loadSvgInto($event,ti,'icon_svg')"></label>
                                            </div>
                                            <textarea v-model="ti.icon_svg" rows="2" placeholder="<svg...></svg>"></textarea>
                                        </div>
                                    </div>
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
                    <button class="btn btn--outline" @click="backFromPost()">← Back</button>
                    <h2 style="font-size:18px;font-weight:600">{{ postForm.id ? 'Edit' : 'New' }} Blog Post</h2>
                    <a v-if="postForm.slug" :href="'/blog/'+postForm.slug+'/'" target="_blank" class="btn btn--outline btn--sm" style="margin-left:auto">↗ View Page</a>
                    <button class="btn btn--primary" @click="savePost">Save</button>
                </div>
                <div class="tabs">
                    <button v-for="t in ['Basic','Content','Tags & TOC','SEO','Extras']" :key="t"
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
                        <!-- Insert shortcode toolbar -->
                        <div style="display:flex;gap:8px;margin-bottom:10px;flex-wrap:wrap">
                            <button class="btn btn--outline btn--sm" @click="openMultiPicker()" style="display:flex;align-items:center;gap:5px">
                                🖼 Insert Gallery
                            </button>
                            <button class="btn btn--outline btn--sm" @click="openMediaShortcodeModal()" style="display:flex;align-items:center;gap:5px">
                                🎬 Insert Media (img+video)
                            </button>
                            <button class="btn btn--outline btn--sm" @click="openCtaModal()" style="display:flex;align-items:center;gap:5px">
                                📣 Insert CTA Block
                            </button>
                            <button class="btn btn--outline btn--sm" @click="openQuoteModal()" style="display:flex;align-items:center;gap:5px">
                                💬 Insert Quote Block
                            </button>
                        </div>
                        <!-- Shortcode instructions -->
                        <details style="margin-bottom:12px;border:1px solid #e0e7ff;border-radius:8px;overflow:hidden">
                            <summary style="padding:10px 14px;background:#f0f4ff;cursor:pointer;font-size:13px;font-weight:600;color:#4338ca;list-style:none;display:flex;align-items:center;gap:6px">
                                📋 How to insert blocks — click to expand
                            </summary>
                            <div style="padding:14px 16px;font-size:13px;line-height:1.7;background:#fff">
                                <p style="margin-bottom:12px;color:#555">Use the toolbar buttons above — they open visual builders and insert blocks directly into the content at the cursor position.</p>
                                <table style="width:100%;border-collapse:collapse;font-size:12px">
                                    <thead><tr style="background:#f0f4ff"><th style="padding:7px 10px;text-align:left;border:1px solid #e2e4e8">Button</th><th style="padding:7px 10px;text-align:left;border:1px solid #e2e4e8">What it inserts</th><th style="padding:7px 10px;text-align:left;border:1px solid #e2e4e8">How it works</th></tr></thead>
                                    <tbody>
                                        <tr><td style="padding:6px 10px;border:1px solid #e2e4e8">🖼 Insert Gallery</td><td style="padding:6px 10px;border:1px solid #e2e4e8">Image carousel / slider</td><td style="padding:6px 10px;border:1px solid #e2e4e8">The media library opens — click photos in the order you want them (numbers appear on images), then click "Insert N Images"</td></tr>
                                        <tr style="background:#fafafa"><td style="padding:6px 10px;border:1px solid #e2e4e8">🎬 Insert Media</td><td style="padding:6px 10px;border:1px solid #e2e4e8">Two-slot media block (photo + video/photo)</td><td style="padding:6px 10px;border:1px solid #e2e4e8">Two slots (left and right). Click a slot → pick from the library. Toggle Library / YouTube for each slot independently.</td></tr>
                                        <tr><td style="padding:6px 10px;border:1px solid #e2e4e8">📣 Insert CTA</td><td style="padding:6px 10px;border:1px solid #e2e4e8">Call-to-action block with buttons</td><td style="padding:6px 10px;border:1px solid #e2e4e8">A form opens — fill in the title and button labels/URLs, then click "Insert into Content"</td></tr>
                                        <tr style="background:#fafafa"><td style="padding:6px 10px;border:1px solid #e2e4e8">💬 Insert Quote</td><td style="padding:6px 10px;border:1px solid #e2e4e8">Two-column purple pull-quote block</td><td style="padding:6px 10px;border:1px solid #e2e4e8">Fill in the left text (short supporting paragraph) and the right text (the main bold quote), then click "Insert"</td></tr>
                                    </tbody>
                                </table>
                                <p style="margin-top:10px;color:#888;font-size:12px">💡 H2, H3, H4 headings automatically appear in the article's sidebar Table of Contents. Use H2 for main sections, H3 for subsections.</p>
                            </div>
                        </details>
                        <div id="ckeditor-mount" style="min-height:500px"></div>
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
                                    <div class="repeat-item__header">
                                        <span class="repeat-item__header-label">{{ t.title || 'TOC Item '+(i+1) }}</span>
                                        <div class="repeat-item__actions"><button class="btn btn--danger btn--sm btn--icon" @click="postForm.toc.splice(i,1)">×</button></div>
                                    </div>
                                    <div class="repeat-item__body">
                                        <div class="form-grid">
                                            <div class="field"><label>TOC Title</label><input v-model="t.title" @input="t.anchor=t.anchor||slugify(t.title)"></div>
                                            <div class="field"><label>Anchor ID</label><input v-model="t.anchor"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div v-if="postTab==='SEO'" class="form-grid cols-1">
                        <div class="field"><label>Meta Title</label><input v-model="postForm.meta_title"></div>
                        <div class="field"><label>Meta Description</label><textarea v-model="postForm.meta_description" rows="3"></textarea></div>
                    </div>

                    <div v-if="postTab==='Extras'">
                        <!-- FAQ -->
                        <div style="margin-bottom:24px">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                                <div style="display:flex;align-items:center;gap:12px">
                                    <strong style="font-size:14px">FAQ Section</strong>
                                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:13px;font-weight:normal">
                                        <input type="checkbox" v-model="postForm.extras.faq_enabled">
                                        Show on page
                                    </label>
                                </div>
                                <button class="btn btn--outline btn--sm" @click="postForm.extras.faq.push({q:'',a:''})">+ Add Item</button>
                            </div>
                            <div v-show="postForm.extras.faq_enabled">
                            <div class="field" style="margin-bottom:12px"><label>FAQ Section Title</label><input v-model="postForm.extras.faq_title"></div>
                            <div class="repeat-list">
                                <div v-for="(item,i) in postForm.extras.faq" :key="i" class="repeat-item">
                                    <div class="repeat-item__header">
                                        <span class="repeat-item__header-label">{{ item.q || 'FAQ Item '+(i+1) }}</span>
                                        <div class="repeat-item__actions"><button class="btn btn--danger btn--sm btn--icon" @click="postForm.extras.faq.splice(i,1)">×</button></div>
                                    </div>
                                    <div class="repeat-item__body">
                                        <div class="form-grid cols-1" style="gap:8px">
                                            <div class="field"><label>Question</label><input v-model="item.q"></div>
                                            <div class="field"><label>Answer</label><textarea v-model="item.a" rows="3"></textarea></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            </div><!-- end v-show faq_enabled -->
                        </div>

                        <!-- Related Articles -->
                        <div>
                            <strong style="font-size:14px;display:block;margin-bottom:8px">Related Articles</strong>
                            <div class="field" style="margin-bottom:12px"><label>Section Title</label><textarea v-model="postForm.extras.articles_title" rows="2"></textarea></div>
                            <div style="display:flex;flex-wrap:wrap;gap:8px">
                                <label v-for="p in postsList.filter(x=>x.id!==postForm.id)" :key="p.id" style="display:flex;align-items:center;gap:6px;cursor:pointer;padding:4px 10px;background:#f8f9fa;border-radius:6px;font-size:13px">
                                    <input type="checkbox" :value="p.id" v-model="postForm.extras.related_post_ids">
                                    {{ p.title || p.slug }}
                                </label>
                            </div>
                        </div>
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
                    + Upload <input type="file" accept="image/*,video/mp4,video/webm" multiple style="display:none" @change="uploadFiles">
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

            <div class="card"><div class="card__head"><h2>Site & Security</h2></div><div class="card__body">
                <div class="form-grid">
                    <div class="field"><label>Site Name</label><input v-model="settingsData.site_name"></div>
                    <div class="field"><label>Contact Email</label><input v-model="settingsData.site_email" type="email"></div>
                    <div class="field field--full"><label>Site Password (leave empty = public access)</label><input v-model="settingsData.site_password" type="text" placeholder="Leave empty to disable password protection"></div>
                </div>
            </div></div>

            <div class="card"><div class="card__head"><h2>Logos</h2></div><div class="card__body">
                <div class="form-grid">
                    <div class="field"><label>Header Logo URL</label><input v-model="settingsData.header_logo_url" placeholder="Leave empty to use default SVG logo"></div>
                    <div class="field"><label>Footer Logo URL</label><input v-model="settingsData.footer_logo_url" placeholder="/assets/img/logo.svg"></div>
                </div>
            </div></div>

            <div class="card"><div class="card__head"><h2>Social Media</h2></div><div class="card__body">
                <div class="form-grid">
                    <div class="field"><label>LinkedIn URL</label><input v-model="settingsData.social_linkedin" placeholder="https://linkedin.com/company/..."></div>
                    <div class="field"><label>WhatsApp Number</label><input v-model="settingsData.social_whatsapp" placeholder="+37255512345"></div>
                    <div class="field"><label>Twitter / X URL</label><input v-model="settingsData.social_twitter" placeholder="https://twitter.com/..."></div>
                    <div class="field"><label>Facebook URL</label><input v-model="settingsData.social_facebook" placeholder="https://facebook.com/..."></div>
                    <div class="field"><label>Instagram URL</label><input v-model="settingsData.social_instagram" placeholder="https://instagram.com/..."></div>
                </div>
            </div></div>

            <div class="card"><div class="card__head"><h2>Footer Links & Badges</h2></div><div class="card__body">
                <div class="form-grid">
                    <div class="field"><label>Footer "Design" Link</label><input v-model="settingsData.footer_design_url" placeholder="https://..."></div>
                    <div class="field"><label>Footer "Development" Link</label><input v-model="settingsData.footer_dev_url" placeholder="https://..."></div>
                    <div class="field"><label>Clutch Badge Link (hero section)</label><input v-model="settingsData.clutch_url" placeholder="https://clutch.co/..."></div>
                    <div class="field"><label>Upwork Badge Link (hero section)</label><input v-model="settingsData.upwork_url" placeholder="https://www.upwork.com/..."></div>
                </div>
            </div></div>

            <div class="card"><div class="card__head"><h2>Case Studies</h2></div><div class="card__body">
                <div class="form-grid">
                    <div class="field"><label>External Link Button Text</label><input v-model="settingsData.case_external_btn_text" placeholder="Visit Website">
                        <div style="margin-top:4px;font-size:11px;color:var(--muted)">Button text on case cards when "Open client website" behavior is selected. Default: Visit Website</div>
                    </div>
                </div>
            </div></div>

            <div class="card"><div class="card__head"><h2>Integrations</h2></div><div class="card__body">
                <div class="form-grid cols-1">
                    <div class="field field--full">
                        <label>Google Tag Manager Code</label>
                        <textarea v-model="settingsData.tag_manager_code" rows="5" placeholder="Paste the GTM &lt;script&gt; snippet here — it will be added to &lt;head&gt; on all pages" style="font-family:monospace;font-size:12px"></textarea>
                        <div style="margin-top:4px;font-size:11px;color:var(--muted)">Paste the GTM snippet from Google Tag Manager → Install → Copy the &lt;script&gt; code. It will be inserted before &lt;/head&gt; on every page.</div>
                    </div>
                </div>
            </div></div>

            <div class="card"><div class="card__head"><h2>Lead Notification Emails</h2></div><div class="card__body">
                <div class="form-grid cols-1">
                    <div class="field field--full">
                        <label>Recipient Emails (comma-separated)</label>
                        <input v-model="settingsData.lead_emails" placeholder="services@sidis.group, hello@expandi.agency">
                        <div style="margin-top:4px;font-size:11px;color:var(--muted)">All contact form submissions will be sent to these addresses. Separate multiple emails with a comma.</div>
                    </div>
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

            <div class="card"><div class="card__head"><h2>Case Studies Page</h2></div><div class="card__body">
                <div class="form-grid">
                    <div class="field"><label>Hero Title ({{ currentLangCode }})</label><input v-model="settingsData.cases_hero_title" placeholder="Case Studies"></div>
                    <div class="field"><label>Hero Subtitle ({{ currentLangCode }})</label><input v-model="settingsData.cases_hero_text" placeholder="See how Sidis Group has helped..."></div>
                    <div class="field"><label>Meta Title ({{ currentLangCode }})</label><input v-model="settingsData.cases_meta_title" placeholder="Falls back to Hero Title"></div>
                    <div class="field field--full"><label>Meta Description ({{ currentLangCode }})</label><textarea v-model="settingsData.cases_meta_description" rows="2" placeholder="Falls back to Hero Subtitle"></textarea></div>
                    <div class="field field--full"><label>Hero Background Image <span style="color:var(--muted);font-size:12px">(shown if no video set)</span></label>
                        <div class="img-picker">
                            <img v-if="settingsData.cases_hero_image_url" :src="settingsData.cases_hero_image_url" class="img-thumb">
                            <button class="btn btn--outline btn--sm" @click="pickMedia(m=>{settingsData.cases_hero_image_url=m.url})">Choose from library</button>
                            <button v-if="settingsData.cases_hero_image_url" class="btn btn--sm" @click="settingsData.cases_hero_image_url=''">Remove</button>
                        </div>
                    </div>
                    <div class="field field--full"><label>Hero Video Path <span style="color:var(--muted);font-size:12px">(autoplay, replaces image when set, e.g. ./assets/video/compressed-v2/Video-cover-case-studies.mp4)</span></label>
                        <input v-model="settingsData.cases_hero_video_path" placeholder="./assets/video/compressed-v2/Video-cover-case-studies.mp4">
                    </div>
                    <div class="field field--full"><label>Hero Video Poster <span style="color:var(--muted);font-size:12px">(shown while video loads)</span></label>
                        <div class="img-picker">
                            <img v-if="settingsData.cases_hero_video_poster_url" :src="settingsData.cases_hero_video_poster_url" class="img-thumb">
                            <button class="btn btn--outline btn--sm" @click="pickMedia(m=>{settingsData.cases_hero_video_poster_url=m.url})">Choose from library</button>
                            <button v-if="settingsData.cases_hero_video_poster_url" class="btn btn--sm" @click="settingsData.cases_hero_video_poster_url=''">Remove</button>
                        </div>
                    </div>
                    <div class="field field--full"><label>OG / Social Share Image</label>
                        <div class="img-picker">
                            <img v-if="settingsData.cases_og_image_url" :src="settingsData.cases_og_image_url" class="img-thumb">
                            <button class="btn btn--outline btn--sm" @click="pickMedia(m=>{settingsData.cases_og_image_url=m.url})">Choose from library</button>
                            <button v-if="settingsData.cases_og_image_url" class="btn btn--sm" @click="settingsData.cases_og_image_url=''">Remove</button>
                        </div>
                        <div class="field-note" style="margin-top:6px;color:var(--muted);font-size:12px">Shown when sharing the link in social media / messengers (1200×630 recommended)</div>
                    </div>
                </div>
            </div></div>

            <div class="card"><div class="card__head"><h2>Blog Page</h2></div><div class="card__body">
                <div class="form-grid">
                    <div class="field"><label>Hero Title ({{ currentLangCode }})</label><input v-model="settingsData.blog_hero_title" placeholder="Trends Blog"></div>
                    <div class="field"><label>Hero Subtitle ({{ currentLangCode }})</label><input v-model="settingsData.blog_hero_text" placeholder="Explore how Sidis Group is shaping the future..."></div>
                    <div class="field"><label>Meta Title ({{ currentLangCode }})</label><input v-model="settingsData.blog_meta_title" placeholder="Falls back to Hero Title"></div>
                    <div class="field field--full"><label>Meta Description ({{ currentLangCode }})</label><textarea v-model="settingsData.blog_meta_description" rows="2" placeholder="Falls back to Hero Subtitle"></textarea></div>
                    <div class="field field--full"><label>Hero Background Image <span style="color:var(--muted);font-size:12px">(shown if no video set)</span></label>
                        <div class="img-picker">
                            <img v-if="settingsData.blog_hero_image_url" :src="settingsData.blog_hero_image_url" class="img-thumb">
                            <button class="btn btn--outline btn--sm" @click="pickMedia(m=>{settingsData.blog_hero_image_url=m.url})">Choose from library</button>
                            <button v-if="settingsData.blog_hero_image_url" class="btn btn--sm" @click="settingsData.blog_hero_image_url=''">Remove</button>
                        </div>
                    </div>
                    <div class="field field--full"><label>Hero Video Path <span style="color:var(--muted);font-size:12px">(autoplay, replaces image when set, e.g. ./assets/video/compressed-v2/Video-cover-blog.mp4)</span></label>
                        <input v-model="settingsData.blog_hero_video_path" placeholder="./assets/video/compressed-v2/Video-cover-blog.mp4">
                    </div>
                    <div class="field field--full"><label>Hero Video Poster <span style="color:var(--muted);font-size:12px">(shown while video loads)</span></label>
                        <div class="img-picker">
                            <img v-if="settingsData.blog_hero_video_poster_url" :src="settingsData.blog_hero_video_poster_url" class="img-thumb">
                            <button class="btn btn--outline btn--sm" @click="pickMedia(m=>{settingsData.blog_hero_video_poster_url=m.url})">Choose from library</button>
                            <button v-if="settingsData.blog_hero_video_poster_url" class="btn btn--sm" @click="settingsData.blog_hero_video_poster_url=''">Remove</button>
                        </div>
                    </div>
                    <div class="field field--full"><label>OG / Social Share Image</label>
                        <div class="img-picker">
                            <img v-if="settingsData.blog_og_image_url" :src="settingsData.blog_og_image_url" class="img-thumb">
                            <button class="btn btn--outline btn--sm" @click="pickMedia(m=>{settingsData.blog_og_image_url=m.url})">Choose from library</button>
                            <button v-if="settingsData.blog_og_image_url" class="btn btn--sm" @click="settingsData.blog_og_image_url=''">Remove</button>
                        </div>
                        <div class="field-note" style="margin-top:6px;color:var(--muted);font-size:12px">Shown when sharing the link in social media / messengers (1200×630 recommended)</div>
                    </div>
                </div>
            </div></div>

            <div class="card"><div class="card__head"><h2>Solutions / Departments / Industries — Aside Panel</h2></div><div class="card__body">
                <div class="form-grid">
                    <div class="field field--full"><label>Demo Image</label>
                        <div class="img-picker">
                            <img v-if="settingsData.aside_demo_image_url" :src="settingsData.aside_demo_image_url" class="img-thumb">
                            <button class="btn btn--outline btn--sm" @click="pickMedia(m=>{settingsData.aside_demo_image_url=m.url})">Choose from library</button>
                            <button v-if="settingsData.aside_demo_image_url" class="btn btn--sm" @click="settingsData.aside_demo_image_url=''">Remove</button>
                        </div>
                    </div>
                    <div class="field"><label>Demo Button Text ({{ currentLangCode }})</label><input v-model="settingsData.aside_demo_btn_text" placeholder="Demo"></div>
                    <div class="field"><label>Demo Button URL</label><input v-model="settingsData.aside_demo_btn_url" placeholder="#getintouch"></div>
                    <div class="field"><label>Contact Button Text ({{ currentLangCode }})</label><input v-model="settingsData.aside_contact_btn_text" placeholder="Contact us"></div>
                    <div class="field"><label>Contact Button URL</label><input v-model="settingsData.aside_contact_btn_url" placeholder="#getintouch"></div>
                </div>
            </div></div>
        </template>

        <!-- ══ USERS ══ -->
        <template v-if="currentView==='users'">
            <div class="section-head">
                <h1>Users</h1>
                <button class="btn btn--primary" @click="openUserForm(null)">+ New User</button>
            </div>
            <div v-if="!editingUser" class="card"><div class="table-wrap"><table>
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Status</th><th style="width:130px"></th></tr></thead>
                <tbody>
                    <tr v-for="u in usersList" :key="u.id">
                        <td>{{ u.name }}</td>
                        <td>{{ u.email }}</td>
                        <td><span :style="{color: u.role==='administrator'?'var(--accent)':'var(--muted)',fontWeight:600}">{{ u.role==='administrator' ? 'Administrator' : 'Manager' }}</span></td>
                        <td><span :style="{color: u.is_active ? 'var(--green)' : 'var(--red)'}">{{ u.is_active ? 'Active' : 'Inactive' }}</span></td>
                        <td style="text-align:right">
                            <button class="btn btn--sm btn--outline" @click="openUserForm(u)">Edit</button>
                            <button class="btn btn--sm" style="background:var(--red);color:#fff;margin-left:6px" @click="deleteUser(u)">Delete</button>
                        </td>
                    </tr>
                    <tr v-if="!usersList.length"><td colspan="5" style="text-align:center;color:var(--muted);padding:24px">No users yet.</td></tr>
                </tbody>
            </table></div></div>

            <div v-if="editingUser" class="card">
                <div class="card__head" style="display:flex;justify-content:space-between;align-items:center">
                    <h2>{{ userForm.id ? 'Edit User' : 'New User' }}</h2>
                    <button class="btn btn--outline" @click="editingUser=false;loadUsers()">← Back</button>
                </div>
                <div class="card__body">
                    <div class="form-grid">
                        <div class="field"><label>Full Name</label><input v-model="userForm.name" placeholder="John Smith"></div>
                        <div class="field"><label>Email</label><input v-model="userForm.email" type="email" placeholder="user@company.com"></div>
                        <div class="field"><label>{{ userForm.id ? 'New Password (leave empty to keep current)' : 'Password' }}</label><input v-model="userForm.password" type="password" :placeholder="userForm.id ? 'Leave empty to keep current' : 'Min 8 characters'"></div>
                        <div class="field"><label>Role</label>
                            <select v-model="userForm.role" style="padding:9px 12px;border:1px solid var(--border);border-radius:8px;font-size:14px">
                                <option value="administrator">Administrator — full access incl. user management</option>
                                <option value="manager">Manager — full access except user management</option>
                            </select>
                        </div>
                        <div class="field" style="flex-direction:row;align-items:center;gap:10px;padding-top:22px">
                            <label class="toggle"><input type="checkbox" v-model="userForm.is_active" :true-value="1" :false-value="0"><span class="toggle__slider"></span></label>
                            <span style="font-size:14px">Active</span>
                        </div>
                    </div>
                    <div style="margin-top:20px">
                        <button class="btn btn--primary" @click="saveUser">Save User</button>
                    </div>
                </div>
            </div>
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
                <div class="field"><label>Icon SVG</label>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                        <div class="svg-preview" v-html="whySlideForm.icon_svg||'<span style=color:#ccc;font-size:10px>No icon</span>'"></div>
                        <label class="btn btn--outline btn--sm" style="cursor:pointer">Upload SVG<input type="file" accept=".svg,image/svg+xml" style="display:none" @change="loadSvgInto($event,whySlideForm,'icon_svg')"></label>
                    </div>
                    <textarea v-model="whySlideForm.icon_svg" rows="3" placeholder="<svg...></svg>"></textarea>
                </div>
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
                <div class="field"><label>Clutch Review URL</label><input v-model="reviewForm.clutch_url" placeholder="https://clutch.co/profile/..."></div>
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
                            <div class="repeat-item__header">
                                <span class="repeat-item__header-label">{{ sub.title || 'Sub-item '+(si+1) }}</span>
                                <div class="repeat-item__actions">
                                    <button class="btn btn--success btn--sm" @click="saveMegaSubitem(sub,cat)">Save</button>
                                    <button class="btn btn--danger btn--sm btn--icon" @click="deleteMegaSubitem(sub.id,cat)">×</button>
                                </div>
                            </div>
                            <div class="repeat-item__body">
                                <div class="form-grid">
                                    <div class="field field--full">
                                        <label>Link to page (icon is taken automatically)</label>
                                        <select :value="sub.url" @change="e => { const p=allNavSolPages.find(x=>x.url===e.target.value); sub.url=e.target.value; if(p&&!sub._title_edited) sub.title=p.title; }" style="padding:8px 10px;border:1px solid var(--border);border-radius:6px;background:var(--bg);color:var(--text);width:100%;font-size:14px">
                                            <option value="#">— select page —</option>
                                            <optgroup label="Solutions">
                                                <option v-for="p in allNavSolPages.filter(x=>x.type==='solution')" :key="p.id" :value="p.url">{{ p.title }}</option>
                                            </optgroup>
                                            <optgroup label="Departments">
                                                <option v-for="p in allNavSolPages.filter(x=>x.type==='department')" :key="p.id" :value="p.url">{{ p.title }}</option>
                                            </optgroup>
                                            <optgroup label="Industries">
                                                <option v-for="p in allNavSolPages.filter(x=>x.type==='industry')" :key="p.id" :value="p.url">{{ p.title }}</option>
                                            </optgroup>
                                        </select>
                                    </div>
                                    <div class="field"><label>Title <span style="font-size:11px;color:var(--muted)">(shown in menu)</span></label><input v-model="sub.title" @input="sub._title_edited=true"></div>
                                    <div class="field field--full"><label>Description <span style="font-size:11px;color:var(--muted)">(shown in menu)</span></label><input v-model="sub.description"></div>
                                </div>
                            </div>
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
                <div class="field"><label>Icon SVG</label>
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
                        <div class="svg-preview" v-html="solutionItemForm.icon_svg||'<span style=color:#ccc;font-size:10px>No icon</span>'"></div>
                        <label class="btn btn--outline btn--sm" style="cursor:pointer">Upload SVG<input type="file" accept=".svg,image/svg+xml" style="display:none" @change="loadSvgInto($event,solutionItemForm,'icon_svg')"></label>
                    </div>
                    <textarea v-model="solutionItemForm.icon_svg" rows="3" placeholder="<svg...></svg>"></textarea>
                </div>
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
        <div class="modal__head">
            <h3>{{ mediaPickerIsMulti ? 'Select Images (click to select, click again to deselect)' : 'Choose Image' }}</h3>
            <button class="btn btn--icon" @click="modals.mediaPicker=false">×</button>
        </div>
        <div class="modal__body">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap">
                <label class="btn btn--outline" style="cursor:pointer">+ Upload New <input type="file" accept="image/*,video/mp4,video/webm" style="display:none" @change="uploadAndPick"></label>
                <template v-if="mediaPickerIsMulti">
                    <span style="font-size:13px;color:var(--muted)">{{ multiSelectedMedia.length }} selected</span>
                    <button class="btn btn--primary" :disabled="!multiSelectedMedia.length" @click="confirmMultiSelect()">Insert {{ multiSelectedMedia.length }} Image{{ multiSelectedMedia.length!==1?'s':'' }}</button>
                </template>
            </div>
            <div class="media-grid">
                <div v-for="m in mediaList" :key="m.id" class="media-item"
                     :style="multiSelectedMedia.find(x=>x.id===m.id) ? 'outline:3px solid var(--accent);border-radius:8px' : ''"
                     @click="mediaPickerIsMulti ? toggleMultiSelect(m) : selectMedia(m)">
                    <img :src="m.url" :alt="m.alt_text" loading="lazy">
                    <div class="media-item__name">{{ m.original_name }}</div>
                    <div v-if="mediaPickerIsMulti && multiSelectedMedia.find(x=>x.id===m.id)"
                         style="position:absolute;top:4px;right:4px;background:var(--accent);color:#fff;border-radius:50%;width:20px;height:20px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700">
                        {{ multiSelectedMedia.findIndex(x=>x.id===m.id)+1 }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Media Shortcode Modal — inline library, no nested modals -->
<div v-if="modals.mediaShortcode" class="modal-overlay" @click.self="modals.mediaShortcode=false">
    <div class="modal" style="max-width:900px;display:flex;flex-direction:column;max-height:90vh">
        <div class="modal__head"><h3>Insert Media Block (2 slots)</h3><button class="btn btn--icon" @click="modals.mediaShortcode=false">×</button></div>
        <div class="modal__body" style="overflow-y:auto;flex:1">
            <!-- Two slots -->
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px">
                <div v-for="(slot,si) in mediaScSlots" :key="si"
                     :style="`border:2px solid ${mediaScActiveSlot===si ? 'var(--accent)' : 'var(--border)'};border-radius:10px;padding:12px;background:${mediaScActiveSlot===si?'#faf0ff':'#fff'}`">
                    <!-- Slot label + mode toggle -->
                    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:8px">
                        <span style="font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px">
                            {{ si===0 ? '◀ Left' : '▶ Right' }}
                            <span v-if="mediaScActiveSlot===si" style="color:var(--accent)"> ← active</span>
                        </span>
                        <div style="display:flex;gap:4px">
                            <button class="btn btn--sm" :style="slot.mode!=='youtube'?'background:var(--accent);color:#fff':''" @click.stop="slot.mode='library'">Library</button>
                            <button class="btn btn--sm" :style="slot.mode==='youtube'?'background:#ff0000;color:#fff':''" @click.stop="slot.mode='youtube';slot.url=''">YouTube</button>
                        </div>
                    </div>
                    <!-- YouTube URL input -->
                    <div v-if="slot.mode==='youtube'" style="margin-bottom:8px">
                        <input v-model="slot.url" placeholder="https://youtube.com/watch?v=..." style="width:100%;padding:7px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px">
                        <div v-if="slot.url && slot.url.match(/youtu/)" style="margin-top:6px;font-size:11px;color:var(--green)">✓ YouTube URL detected</div>
                    </div>
                    <!-- Preview from library -->
                    <div v-else>
                        <div v-if="slot.url" style="position:relative;cursor:pointer" @click="mediaScActiveSlot=si">
                            <img v-if="!slot.url.match(/\.(mp4|webm|mov)/i)" :src="slot.url" style="width:100%;height:90px;object-fit:cover;border-radius:6px;display:block">
                            <div v-else style="background:#111;color:#fff;height:90px;border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:13px">🎬 {{ slot.url.split('/').pop() }}</div>
                            <button @click.stop="slot.url=''" style="position:absolute;top:4px;right:4px;background:rgba(0,0,0,.65);color:#fff;border:none;border-radius:50%;width:22px;height:22px;cursor:pointer;font-size:16px;line-height:1.1">×</button>
                        </div>
                        <div v-else style="height:90px;border:2px dashed var(--border);border-radius:6px;display:flex;align-items:center;justify-content:center;color:var(--muted);font-size:12px;cursor:pointer" @click="mediaScActiveSlot=si">
                            Click here, then pick image below
                        </div>
                    </div>
                </div>
            </div>
            <!-- Active slot indicator + library -->
            <template v-if="mediaScSlots[mediaScActiveSlot].mode!=='youtube'">
                <div style="font-size:13px;font-weight:600;color:var(--accent);margin-bottom:10px;padding:8px 12px;background:#faf0ff;border-radius:6px">
                    Click any image below → goes into the <strong>{{ mediaScActiveSlot===0 ? 'LEFT' : 'RIGHT' }}</strong> slot
                </div>
                <div style="display:flex;gap:8px;margin-bottom:12px">
                    <label class="btn btn--outline btn--sm" style="cursor:pointer">+ Upload New<input type="file" accept="image/*,video/*" style="display:none" @change="uploadAndPick"></label>
                </div>
                <div class="media-grid">
                    <div v-for="m in mediaList" :key="m.id" class="media-item" @click="mediaScPickSlot(m)">
                        <img :src="m.url" :alt="m.alt_text" loading="lazy">
                        <div class="media-item__name">{{ m.original_name }}</div>
                    </div>
                </div>
            </template>
        </div>
        <div class="modal__foot" style="border-top:1px solid var(--border);padding:12px 20px;display:flex;justify-content:space-between;align-items:center">
            <code style="font-size:11px;color:var(--muted);flex:1;margin-right:12px;word-break:break-all">{{ buildMediaShortcodeFromSlots() }}</code>
            <div style="display:flex;gap:8px">
                <button class="btn btn--outline" @click="modals.mediaShortcode=false">Cancel</button>
                <button class="btn btn--primary" @click="confirmMediaShortcode()" :disabled="!mediaScSlots[0].url&&!mediaScSlots[1].url">Insert into Content</button>
            </div>
        </div>
    </div>
</div>

<!-- CTA Modal -->
<div v-if="modals.ctaModal" class="modal-overlay" @click.self="modals.ctaModal=false">
    <div class="modal" style="max-width:560px">
        <div class="modal__head"><h3>📣 Insert CTA Block</h3><button class="btn btn--icon" @click="modals.ctaModal=false">×</button></div>
        <div class="modal__body">
            <div class="form-grid cols-1" style="gap:12px">
                <div class="field"><label>Title</label><input v-model="ctaForm.title" placeholder="Curious which solution fits your business needs?"></div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">
                    <div class="field"><label>Button 1 Text</label><input v-model="ctaForm.btn1_text" placeholder="View Our Presentation"></div>
                    <div class="field"><label>Button 1 URL</label><input v-model="ctaForm.btn1_url" placeholder="#"></div>
                    <div class="field"><label>Button 2 Text</label><input v-model="ctaForm.btn2_text" placeholder="Free audit"></div>
                    <div class="field"><label>Button 2 URL</label><input v-model="ctaForm.btn2_url" placeholder="#getintouch"></div>
                </div>
            </div>
            <code style="display:block;margin-top:14px;font-size:11px;background:#f8f9fa;padding:8px;border-radius:6px;word-break:break-all">{{ buildCtaShortcode() }}</code>
        </div>
        <div class="modal__foot">
            <button class="btn btn--outline" @click="modals.ctaModal=false">Cancel</button>
            <button class="btn btn--primary" @click="confirmCta()">Insert into Content</button>
        </div>
    </div>
</div>

<div v-if="modals.quoteModal" class="modal-overlay" @click.self="modals.quoteModal=false">
    <div class="modal" style="max-width:640px">
        <div class="modal__head"><h3>💬 Insert Quote Block</h3><button class="btn btn--icon" @click="modals.quoteModal=false">×</button></div>
        <div class="modal__body">
            <!-- Controls row -->
            <div style="display:flex;gap:16px;align-items:flex-end;margin-bottom:16px;flex-wrap:wrap">
                <div class="field" style="flex:0 0 auto">
                    <label>Columns</label>
                    <div style="display:flex;gap:8px;margin-top:4px">
                        <label style="display:flex;align-items:center;gap:5px;cursor:pointer;font-size:14px"><input type="radio" v-model.number="quoteForm.cols" :value="2"> 2 columns</label>
                        <label style="display:flex;align-items:center;gap:5px;cursor:pointer;font-size:14px"><input type="radio" v-model.number="quoteForm.cols" :value="1"> 1 column (full width)</label>
                    </div>
                </div>
                <div class="field" style="flex:0 0 130px">
                    <label>Right font size (px)</label>
                    <input type="number" v-model.number="quoteForm.right_size" min="10" max="60" style="padding:6px 10px;border:1px solid var(--border);border-radius:6px;width:100%">
                </div>
                <div v-if="quoteForm.cols==2" class="field" style="flex:0 0 130px">
                    <label>Left font size (px)</label>
                    <input type="number" v-model.number="quoteForm.left_size" min="10" max="60" style="padding:6px 10px;border:1px solid var(--border);border-radius:6px;width:100%">
                </div>
            </div>
            <!-- Text fields -->
            <div :style="{display:'grid',gridTemplateColumns:quoteForm.cols==2?'1fr 1fr':'1fr',gap:'16px',marginBottom:'16px'}">
                <div>
                    <div style="font-size:12px;font-weight:600;margin-bottom:6px;color:var(--muted)">RIGHT — main text (large, bold)</div>
                    <textarea v-model="quoteForm.right" rows="5" style="width:100%;padding:8px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px;resize:vertical" placeholder="At first, automation might seem complex and a bit intimidating..."></textarea>
                </div>
                <div v-if="quoteForm.cols==2">
                    <div style="font-size:12px;font-weight:600;margin-bottom:6px;color:var(--muted)">LEFT — supporting text (smaller)</div>
                    <textarea v-model="quoteForm.left" rows="5" style="width:100%;padding:8px 10px;border:1px solid var(--border);border-radius:6px;font-size:13px;resize:vertical" placeholder="I remember when I started my entrepreneurial career..."></textarea>
                </div>
            </div>
            <!-- Preview -->
            <div :style="{background:'linear-gradient(135deg,#7c2891 0%,#3d1260 100%)',borderRadius:'14px',padding:'24px 32px',color:'#fff',display:'grid',gridTemplateColumns:quoteForm.cols==2?'1fr 1fr':'1fr',gap:'40px'}">
                <div :style="{fontWeight:700,fontSize:quoteForm.right_size+'px',lineHeight:'1.4',whiteSpace:'pre-wrap'}">{{ quoteForm.right || 'Main text...' }}</div>
                <div v-if="quoteForm.cols==2" :style="{fontSize:quoteForm.left_size+'px',lineHeight:'1.6',opacity:0.9,whiteSpace:'pre-wrap'}">{{ quoteForm.left || 'Supporting text...' }}</div>
            </div>
            <code style="display:block;margin-top:10px;font-size:11px;background:#f8f9fa;padding:8px;border-radius:6px;word-break:break-all">{{ buildQuoteShortcode() }}</code>
        </div>
        <div class="modal__foot">
            <button class="btn btn--outline" @click="modals.quoteModal=false">Cancel</button>
            <button class="btn btn--primary" @click="confirmQuote()">Insert into Content</button>
        </div>
    </div>
</div>

</div><!-- #app -->

<script>
const { createApp, ref, reactive, computed, onMounted, nextTick } = Vue;

createApp({
    setup() {
        const adminName = ref('<?= addslashes($admin['name'] ?? 'Admin') ?>');
        const isAdmin = ref(<?= (($_SESSION['admin_role'] ?? 'administrator') === 'administrator') ? 'true' : 'false' ?>);
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
            {id:'reviews', label:'Reviews', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>'},
            {id:'terms', label:'Taxonomy', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><path d="M20 4H8.12A5.37 5.37 0 0012 6a5.37 5.37 0 00-3.88 2H20v-4z"/><path d="M20 14H8.12A5.37 5.37 0 0012 16a5.37 5.37 0 00-3.88 2H20v-4z"/></svg>'},
            {id:'media', label:'Media Library', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>'},
            {id:'languages', label:'Languages', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>'},
            {id:'settings', label:'Settings', icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>'},
            {id:'users', label:'Users', adminOnly:true, icon:'<svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>'},
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

        // Load SVG file into a form field via FileReader
        const loadSvgInto = (event, obj, field) => {
            const file = event.target.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => { obj[field] = e.target.result; };
            reader.readAsText(file);
            event.target.value = '';
        };

        const uploadHeroVideo = async (event, field) => {
            const file = event.target.files[0];
            if (!file) return;
            event.target.value = '';
            const fd = new FormData();
            fd.append('file', file);
            try {
                const r = await fetch('/admin/api/upload_video.php', {method:'POST', body:fd});
                const j = await r.json();
                if (j.ok) settingsData[field] = j.path;
                else showAlert(j.error || 'Upload failed');
            } catch(e) { showAlert('Upload error: ' + e.message); }
        };

        /* ─── Languages ─────────────────────────────────────────────────── */
        const langForm = reactive({id:0, code:'', name:'', is_active:1});
        const modals = reactive({lang:false, whySlide:false, review:false, nav:false, mega:false,
                                  solutionItem:false, term:false, author:false, mediaPicker:false, mediaShortcode:false, ctaModal:false, quoteModal:false});

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
        const allSolPages = ref([]);
        const whySlideForm = reactive({id:0,sort_order:0,title:'',text:'',icon_svg:''});
        const reviewForm = reactive({id:0,sort_order:0,quote:'',text:'',author_name:'',author_title:'',linkedin_url:'',clutch_url:'',author_image_id:null,author_image_url:'',is_active:1});

        const loadHome = async () => {
            const d = await api(`/admin/api/home.php?lang_id=${langId.value}`);
            if (!Array.isArray(d.solutions_ids)) d.solutions_ids = [];
            if (!Array.isArray(d.departments_ids)) d.departments_ids = [];
            if (!Array.isArray(d.industries_ids)) d.industries_ids = [];
            if (!Array.isArray(d.featured_cases_ids)) d.featured_cases_ids = [];
            d.presentation_enabled = d.presentation_enabled ? 1 : 0;
            Object.assign(homeData, d);
            if (!allCases.value.length) {
                allCases.value = await api(`/admin/api/cases.php?lang_id=${langId.value}`);
            }
            whySlides.value = await api(`/admin/api/home.php?action=why_slides&lang_id=${langId.value}`);
            partnerLogos.value = await api(`/admin/api/home.php?action=partner_logos&lang_id=${langId.value}`);
            autoImages.value = await api(`/admin/api/home.php?action=auto_images&lang_id=${langId.value}`);
            reviews.value = await api(`/admin/api/home.php?action=reviews&lang_id=${langId.value}`);
            allSolPages.value = await api(`/admin/api/sol_pages.php?action=list&lang_id=${langId.value}`);
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
        const openReviewModal = (r=null) => { Object.assign(reviewForm,r?{...r,author_image_url:r.author_image_url||''}:{id:0,sort_order:reviews.value.length,quote:'',text:'',author_name:'',author_title:'',linkedin_url:'',clutch_url:'',author_image_id:null,author_image_url:'',is_active:1}); modals.review=true; };
        const saveReview = async () => {
            await api(`/admin/api/home.php?action=save_review&lang_id=${langId.value}${reviewForm.id?'&id='+reviewForm.id:''}`,{method:'POST',body:JSON.stringify(reviewForm)});
            modals.review=false; reviews.value = await api(`/admin/api/home.php?action=reviews&lang_id=${langId.value}`); showAlert('Saved!');
        };
        const deleteReview = async id => { if (!confirm('Delete?')) return; await api(`/admin/api/home.php?action=delete_review&id=${id}`,{method:'POST'}); reviews.value = await api(`/admin/api/home.php?action=reviews&lang_id=${langId.value}`); };
        const addPartnerLogo = async img => { await api(`/admin/api/home.php?action=save_partner_logo&lang_id=${langId.value}`,{method:'POST',body:JSON.stringify({image_id:img.id,alt_text:img.alt_text||'',sort_order:partnerLogos.value.length})}); partnerLogos.value = await api(`/admin/api/home.php?action=partner_logos&lang_id=${langId.value}`); };
        const deletePartnerLogo = async id => { await api(`/admin/api/home.php?action=delete_partner_logo&id=${id}`,{method:'POST'}); partnerLogos.value = await api(`/admin/api/home.php?action=partner_logos&lang_id=${langId.value}`); };
        const dragLogoIdx = ref(null);
        const onLogoDragStart = i => { dragLogoIdx.value = i; };
        const onLogoDragOver = (e, i) => {
            e.preventDefault();
            if (dragLogoIdx.value === null || dragLogoIdx.value === i) return;
            const arr = [...partnerLogos.value];
            const [moved] = arr.splice(dragLogoIdx.value, 1);
            arr.splice(i, 0, moved);
            partnerLogos.value = arr;
            dragLogoIdx.value = i;
        };
        const onLogoDrop = async () => {
            dragLogoIdx.value = null;
            await api(`/admin/api/home.php?action=reorder_partner_logos`, {method:'POST', body: JSON.stringify({ids: partnerLogos.value.map(l => l.id)})});
        };
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
        const allNavSolPages = ref([]);
        const openMegaEditor = async item => {
            megaNavItem.value = item;
            megaCategories.value = await api(`/admin/api/nav.php?action=mega_categories&id=${item.id}&lang_id=${langId.value}`);
            if (!allNavSolPages.value.length) {
                const pages = await api(`/admin/api/solutions.php?action=pages&lang_id=${langId.value}`);
                allNavSolPages.value = (pages||[]).map(p=>({
                    id: p.id, type: p.type, title: p.title||p.slug, icon_svg: p.icon_svg||'',
                    url: '/'+(p.type==='industry'?'industries':p.type+'s')+'/'+p.slug+'/'
                }));
            }
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

        /* ─── Solution Pages ─────────────────────────────────────────────── */
        const solPagesTab   = ref('solution');
        const solPagesList  = ref([]);
        const editingSolPage = ref(false);
        const editingSolBlocks = ref(false);
        const editingSolPageObj = ref(null);
        const solPageBlocks = ref([]);
        const solPageForm = reactive({id:0,type:'solution',slug:'',title:'',description:'',btn1_text:'View Our Presentation',btn2_text:'Free audit',icon_svg:'',icon_svg_white:'',image_id:null,image_url:'',sort_order:0,is_active:1,meta_title:'',meta_description:''});
        const allReviews = ref([]);
        const allCases   = ref([]);
        const allPosts   = ref([]);

        const loadSolPages = async () => {
            solPagesList.value = await api(`/admin/api/sol_pages.php?action=list&lang_id=${langId.value}`);
        };
        const openSolPageEditor = (p=null) => {
            Object.assign(solPageForm, p ? {...p,image_url:p.image_url||'',icon_svg:p.icon_svg||'',icon_svg_white:p.icon_svg_white||''} : {id:0,type:solPagesTab.value,slug:'',title:'',description:'',btn1_text:'View Our Presentation',btn2_text:'Free audit',icon_svg:'',icon_svg_white:'',image_id:null,image_url:'',sort_order:0,is_active:1,meta_title:'',meta_description:''});
            editingSolBlocks.value = false;
            editingSolPage.value = true;
        };
        const saveSolPage = async () => {
            const r = await api(`/admin/api/sol_pages.php?action=save_page&lang_id=${langId.value}${solPageForm.id?'&id='+solPageForm.id:''}`,{method:'POST',body:JSON.stringify(solPageForm)});
            if (r.ok) { editingSolPage.value=false; await loadSolPages(); showAlert('Page saved!'); }
        };
        const deleteSolPage = async p => {
            if (!confirm(`Delete "${p.title||p.slug}"?`)) return;
            await api(`/admin/api/sol_pages.php?action=delete_page&id=${p.id}`,{method:'POST'});
            await loadSolPages(); showAlert('Deleted');
        };
        const openSolBlockEditor = async p => {
            editingSolPageObj.value = p;
            const [blocks, revs, cases, posts] = await Promise.all([
                api(`/admin/api/sol_pages.php?action=blocks&id=${p.id}&lang_id=${langId.value}`),
                api(`/admin/api/home.php?action=reviews&lang_id=${langId.value}`),
                api(`/admin/api/cases.php?action=list&lang_id=${langId.value}`),
                api(`/admin/api/posts.php?lang_id=${langId.value}`),
            ]);
            allReviews.value = revs;
            allCases.value   = Array.isArray(cases) ? cases : (cases.items || []);
            allPosts.value   = Array.isArray(posts) ? posts : (posts.items || []);
            solPageBlocks.value = blocks.map(b => {
                const co = b.content_obj || {};
                if (!Array.isArray(co.selected_ids)) co.selected_ids = [];
                return {...b, _open: false, content_obj: co};
            });
            editingSolBlocks.value = true;
            editingSolPage.value = false;
        };
        const saveSolBlock = async blk => {
            await api(`/admin/api/sol_pages.php?action=save_block&lang_id=${langId.value}`,{method:'POST',body:JSON.stringify({page_id:editingSolPageObj.value?.id,block_key:blk.block_key,label:blk.label,sort_order:blk.sort_order,is_active:blk.is_active,content:blk.content_obj})});
            showAlert('Block saved!');
        };
        const reorderSolBlocks = async () => {
            const payload = { blocks: solPageBlocks.value.map((b,i)=>({page_id:editingSolPageObj.value?.id,block_key:b.block_key,is_active:b.is_active,sort_order:i})) };
            await api(`/admin/api/sol_pages.php?action=reorder_blocks&lang_id=${langId.value}`,{method:'POST',body:JSON.stringify(payload)});
        };

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
        const caseForm = reactive({id:0,slug:'',title:'',company_name:'',description:'',overview_text:'',location:'',cooperation_period:'',key_results_text:'',services_text:'',meta_title:'',meta_description:'',is_active:1,is_featured:0,sort_order:0,link_behavior:'case_page',external_url:'',featured_image_id:null,company_logo_id:null,image_url:'',logo_url:'',challenges:[],tech_items:[],term_ids:[]});

        const loadCases = async () => { casesList.value = await api(`/admin/api/cases.php?lang_id=${langId.value}`); };
        const openCaseEditor = async (c=null) => {
            caseTab.value='Basic';
            if (c) {
                const full = await api(`/admin/api/cases.php?id=${c.id}&lang_id=${langId.value}`);
                Object.assign(caseForm,{...full,link_behavior:full.link_behavior||'case_page',external_url:full.external_url||'',key_results_text:(full.key_results||[]).map(r=>r.text).join('\n'),services_text:(full.services||[]).map(s=>s.service_name).join('\n'),challenges:full.challenges.map(ch=>({id:ch.id,title:ch.ch_title||'',text:ch.ch_text||''})),tech_items:full.tech_items||[],term_ids:full.term_ids||[]});
            } else {
                Object.assign(caseForm,{id:0,slug:'',title:'',company_name:'',description:'',overview_text:'',location:'',cooperation_period:'',key_results_text:'',services_text:'',meta_title:'',meta_description:'',is_active:1,is_featured:0,sort_order:0,link_behavior:'case_page',external_url:'',featured_image_id:null,company_logo_id:null,image_url:'',logo_url:'',challenges:[],tech_items:[],term_ids:[]});
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
        const defaultExtras = () => ({cta_title:'',cta_btn1_text:'View Our Presentation',cta_btn1_url:'/assets/Sidis-Group.pdf',cta_btn2_text:'Free audit',cta_btn2_url:'#getintouch',faq_title:'Questions & answers',faq_enabled:true,faq:[],articles_title:'Latest Automation\nInsights',related_post_ids:[],_gallery_imgs:[],_media_img_url:'',_media_video_url:''});
        const postForm = reactive({id:0,slug:'',title:'',subtitle:'',content:'',excerpt:'',meta_title:'',meta_description:'',is_active:1,featured_image_id:null,image_url:'',author_id:null,published_at:'',tags_text:'',tags:[],toc:[],extras:defaultExtras()});

        const loadPosts = async () => { postsList.value = await api(`/admin/api/posts.php?lang_id=${langId.value}`); };
        const loadAuthors = async () => { authorsList.value = await api(`/admin/api/authors.php?lang_id=${langId.value}`); };
        const openPostEditor = async (p=null) => {
            postTab.value='Basic'; destroyTinyMCE();
            if (p) {
                const full = await api(`/admin/api/posts.php?id=${p.id}&lang_id=${langId.value}`);
                const loadedExtras = Object.assign(defaultExtras(), full.extras||{});
                Object.assign(postForm,{...full,tags_text:(full.tags||[]).map(t=>t.tag_text).join('\n'),toc:full.toc||[],extras:loadedExtras});
            } else {
                Object.assign(postForm,{id:0,slug:'',title:'',subtitle:'',content:'',excerpt:'',meta_title:'',meta_description:'',is_active:1,featured_image_id:null,image_url:'',author_id:null,published_at:new Date().toISOString().slice(0,16),tags_text:'',tags:[],toc:[],extras:defaultExtras()});
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
                if (_ckEditor) postForm.content = _ckEditor.getData();
                destroyTinyMCE();
                postTab.value=t;
            }
        };
        let _ckEditor = null;
        const initTinyMCE = async () => {
            if (_ckEditor) return;
            const el = document.getElementById('ckeditor-mount');
            if (!el) return;
            const { ClassicEditor, Essentials, Bold, Italic, Underline, Link, List, ListProperties,
                    Indent, IndentBlock,
                    Heading, SourceEditing, Table, TableToolbar, Paragraph,
                    GeneralHtmlSupport, HtmlComment, BlockQuote } = CKEDITOR;
            _ckEditor = await ClassicEditor.create(el, {
                plugins: [ Essentials, Bold, Italic, Underline, Link, List, ListProperties,
                           Indent, IndentBlock,
                           Heading, SourceEditing, Table, TableToolbar, Paragraph,
                           GeneralHtmlSupport, HtmlComment, BlockQuote ],
                toolbar: ['undo','redo','|','heading','|','bold','italic','underline','|',
                          'bulletedList','numberedList','|','outdent','indent','|',
                          'blockQuote','|','link','insertTable','|','sourceEditing'],
                list: { properties: { styles: true, startIndex: true, reversed: true } },
                heading: { options: [
                    {model:'paragraph',title:'Paragraph',class:'ck-heading_paragraph'},
                    {model:'heading2',view:'h2',title:'H2',class:'ck-heading_heading2'},
                    {model:'heading3',view:'h3',title:'H3',class:'ck-heading_heading3'},
                    {model:'heading4',view:'h4',title:'H4',class:'ck-heading_heading4'},
                ]},
                htmlSupport: { allow: [{name:/^(div|span|section|article|figure|img|video|source|br|ul|ol|li|table|tr|td|th|tbody|thead|blockquote|pre|code|a|p|h[1-6]|strong|em|u|s)$/,attributes:true,classes:true,styles:true}] },
            });
            _ckEditor.setData(postForm.content || '');
            _ckEditor.model.document.on('change:data', () => { postForm.content = _ckEditor.getData(); });
        };
        const destroyTinyMCE = () => {
            if (_ckEditor) { postForm.content = _ckEditor.getData(); _ckEditor.destroy(); _ckEditor = null; }
        };
        const backFromPost = async () => {
            if (_ckEditor) {
                try { postForm.content = _ckEditor.getData(); await _ckEditor.destroy(); } catch(e) {}
                _ckEditor = null;
            }
            editingPost.value = null;
            loadPosts();
        };
        const savePost = async () => {
            if (_ckEditor) postForm.content = _ckEditor.getData();
            const payload = {...postForm, tags:postForm.tags_text.split('\n').filter(l=>l.trim()), extras:postForm.extras};
            const r = await api(`/admin/api/posts.php?action=save&lang_id=${langId.value}${postForm.id?'&id='+postForm.id:''}`,{method:'POST',body:JSON.stringify(payload)});
            if (r.ok) { postForm.id=r.id; showAlert('Post saved!'); }
            else showAlert(r.error||'Error','error');
        };
        // Gallery builder helpers
        const galleryAddImage = m => {
            if (!Array.isArray(postForm.extras._gallery_imgs)) postForm.extras._gallery_imgs = [];
            postForm.extras._gallery_imgs.push({url: m.url});
        };
        const galleryMoveImg = (i, dir) => {
            const arr = postForm.extras._gallery_imgs;
            const j = i + dir;
            if (j < 0 || j >= arr.length) return;
            [arr[i], arr[j]] = [arr[j], arr[i]];
        };
        const buildGalleryShortcode = () => {
            const urls = (postForm.extras._gallery_imgs || []).map(x => x.url).join(',');
            return urls ? `[gallery img="${urls}"]` : '';
        };
        const buildMediaShortcode = () => {
            const img = postForm.extras._media_img_url || '';
            const video = postForm.extras._media_video_url || '';
            return `[media img="${img}" video="${video}"]`;
        };
        const insertShortcode = sc => {
            if (!sc) return;
            if (_ckEditor) {
                _ckEditor.model.change(writer => {
                    const pos = _ckEditor.model.document.selection.getFirstPosition();
                    writer.insertText(sc, pos);
                });
                showAlert('Shortcode inserted into content!');
            } else {
                navigator.clipboard.writeText(sc);
                showAlert('Shortcode copied to clipboard (switch to Content tab first to insert).');
            }
        };
        // Legacy aliases (keep for any existing refs)
        const copyGalleryShortcode = () => { const sc = buildGalleryShortcode(); if(sc){navigator.clipboard.writeText(sc);showAlert('Copied!');} };
        const copyMediaShortcode  = () => { const sc = buildMediaShortcode();  navigator.clipboard.writeText(sc); showAlert('Copied!'); };
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
        const mediaPickerIsMulti = ref(false);
        const multiSelectedMedia = ref([]);
        const pickMedia = callback => { mediaPickerIsMulti.value=false; multiSelectedMedia.value=[]; mediaPickerCallback=callback; modals.mediaPicker=true; if(!mediaList.value.length) loadMedia(); };
        const selectMedia = m => { if(mediaPickerCallback) mediaPickerCallback(m); modals.mediaPicker=false; mediaPickerCallback=null; };
        // Multi-select mode
        const openMultiPicker = () => { mediaPickerIsMulti.value=true; multiSelectedMedia.value=[]; modals.mediaPicker=true; if(!mediaList.value.length) loadMedia(); };
        const toggleMultiSelect = m => {
            const idx = multiSelectedMedia.value.findIndex(x=>x.id===m.id);
            if (idx>=0) multiSelectedMedia.value.splice(idx,1); else multiSelectedMedia.value.push(m);
        };
        const confirmMultiSelect = () => {
            if (!multiSelectedMedia.value.length) return;
            const urls = multiSelectedMedia.value.map(m=>m.url).join(',');
            const sc = `[gallery img="${urls}"]`;
            insertShortcode(sc);
            // Also update extras gallery for Extras tab
            postForm.extras._gallery_imgs = multiSelectedMedia.value.map(m=>({url:m.url}));
            modals.mediaPicker = false;
            multiSelectedMedia.value = [];
            mediaPickerIsMulti.value = false;
        };
        // Media shortcode modal — two flexible slots, inline library
        const mediaScSlots      = ref([{url:''},{url:''}]);
        const mediaScActiveSlot = ref(0);
        const openMediaShortcodeModal = () => {
            mediaScSlots.value = [{url:'',mode:'library'},{url:'',mode:'library'}];
            mediaScActiveSlot.value = 0;
            modals.mediaShortcode = true;
            if (!mediaList.value.length) loadMedia();
        };
        const mediaScPickSlot = m => { mediaScSlots.value[mediaScActiveSlot.value].url = m.url; };
        const buildMediaShortcodeFromSlots = () => {
            const a = mediaScSlots.value[0].url || '';
            const b = mediaScSlots.value[1].url || '';
            if (!a && !b) return '';
            return `[media img="${a}" video="${b}"]`;
        };
        const confirmMediaShortcode = () => {
            const sc = buildMediaShortcodeFromSlots();
            if (!sc) return;
            insertShortcode(sc);
            postForm.extras._media_img_url   = mediaScSlots.value[0].url;
            postForm.extras._media_video_url = mediaScSlots.value[1].url;
            modals.mediaShortcode = false;
        };
        // CTA modal
        const ctaForm = reactive({title:'', btn1_text:'View Our Presentation', btn1_url:'/assets/Sidis-Group.pdf', btn2_text:'Free audit', btn2_url:'#getintouch'});
        const openCtaModal = () => { modals.ctaModal = true; };
        const quoteForm = reactive({left:'', right:'', cols:2, left_size:16, right_size:26});
        const openQuoteModal = () => { modals.quoteModal = true; };
        const buildQuoteShortcode = () => {
            const esc = s => s.replace(/"/g, '&quot;');
            let sc = `[quote cols="${quoteForm.cols}" right_size="${quoteForm.right_size}" right="${esc(quoteForm.right)}"`;
            if (quoteForm.cols==2) sc += ` left_size="${quoteForm.left_size}" left="${esc(quoteForm.left)}"`;
            return sc + ']';
        };
        const confirmQuote = () => { insertShortcode(buildQuoteShortcode()); quoteForm.left=''; quoteForm.right=''; quoteForm.cols=2; quoteForm.left_size=16; quoteForm.right_size=26; modals.quoteModal=false; };
        const buildCtaShortcode = () => {
            const e = (s) => s.replace(/"/g, '&quot;');
            return `[cta title="${e(ctaForm.title)}" btn1_text="${e(ctaForm.btn1_text)}" btn1_url="${e(ctaForm.btn1_url)}" btn2_text="${e(ctaForm.btn2_text)}" btn2_url="${e(ctaForm.btn2_url)}"]`;
        };
        const confirmCta = () => {
            insertShortcode(buildCtaShortcode());
            modals.ctaModal = false;
        };

        // Legacy compat refs (Extras tab still uses these)
        const mediaScImg   = computed(()=>mediaScSlots.value[0].url);
        const mediaScVideo = computed(()=>mediaScSlots.value[1].url);
        const deleteMedia = async id => { if(!confirm('Delete this file?')) return; await api(`/admin/api/media.php?action=delete&id=${id}`,{method:'POST',body:'{}'}); await loadMedia(); };
        const copyUrl = url => { navigator.clipboard.writeText(location.origin+url); showAlert('URL copied!'); };

        /* ─── Users ─────────────────────────────────────────────────────── */
        const usersList   = ref([]);
        const editingUser = ref(false);
        const userForm    = reactive({id:0, name:'', email:'', password:'', role:'manager', is_active:1});
        const loadUsers   = async () => { usersList.value = await api('/admin/api/users.php') || []; };
        const openUserForm = (u) => {
            Object.assign(userForm, u ? {...u, password:''} : {id:0,name:'',email:'',password:'',role:'manager',is_active:1});
            editingUser.value = true;
        };
        const saveUser = async () => {
            const r = await api(`/admin/api/users.php?action=save${userForm.id?'&id='+userForm.id:''}`, {method:'POST',body:JSON.stringify(userForm)});
            if (r?.error) { showAlert(r.error, 'error'); return; }
            showAlert('User saved!');
            editingUser.value = false;
            await loadUsers();
        };
        const deleteUser = async (u) => {
            if (!confirm(`Delete user "${u.name}" (${u.email})?`)) return;
            const r = await api(`/admin/api/users.php?action=delete&id=${u.id}`, {method:'POST',body:'{}'});
            if (r?.error) { showAlert(r.error, 'error'); return; }
            showAlert('User deleted.');
            await loadUsers();
        };

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

        /* ─── Home Blocks ───────────────────────────────────────────────── */
        const homeBlocksList = ref([]);
        const loadBlocks = async () => { homeBlocksList.value = await api('/admin/api/blocks.php'); };
        const toggleBlock = async block => {
            block.is_active = block.is_active ? 0 : 1;
            await api(`/admin/api/blocks.php?action=toggle&id=${block.id}`,{method:'POST',body:'{}'});
        };
        const moveBlock = (i, dir) => {
            const arr = homeBlocksList.value;
            const j = i + dir;
            if (j < 0 || j >= arr.length) return;
            [arr[i], arr[j]] = [arr[j], arr[i]];
        };
        const saveBlocksOrder = async () => {
            const order = homeBlocksList.value.map(b => b.id);
            await api('/admin/api/blocks.php?action=reorder',{method:'POST',body:JSON.stringify({order})});
            showAlert('Block order saved!');
            await loadBlocks();
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
        const loadReviews = async () => {
            reviews.value = await api(`/admin/api/home.php?action=reviews&lang_id=${langId.value}`);
        };

        const onLangChange = () => {
            const v = currentView.value;
            if (v==='home') loadHome();
            else if (v==='cases') { editingCase.value=null; loadCases(); }
            else if (v==='blog') { editingPost.value=null; loadPosts(); }
            else if (v==='nav') loadNav();
            else if (v==='reviews') loadReviews();
            else if (v==='terms') loadTerms();
            else if (v==='settings') loadSettings();
        };

        /* ─── View change watcher ───────────────────────────────────────── */
        const onViewChange = v => {
            if (v==='home') loadHome();
            else if (v==='cases') { editingCase.value=null; loadCases(); loadTerms(); }
            else if (v==='blog') { editingPost.value=null; loadPosts(); loadAuthors(); }
            else if (v==='nav') loadNav();
            else if (v==='reviews') loadReviews();
            else if (v==='solutions') { loadSolPages(); }
            else if (v==='terms') loadTerms();
            else if (v==='authors') loadAuthors();
            else if (v==='media') loadMedia();
            else if (v==='languages') loadLanguages();
            else if (v==='settings') loadSettings();
            else if (v==='users') { editingUser.value=false; loadUsers(); }
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
            adminName, isAdmin, currentView, currentViewLabel, langId, languages, langList, alert, stats,
            usersList, editingUser, userForm, openUserForm, saveUser, deleteUser,
            navItems, currentLangCode,
            modals, langForm, openLangModal, saveLang, deleteLang, setDefaultLang,
            homeTab, homeData, whySlides, partnerLogos, autoImages, reviews,
            whySlideForm, reviewForm,
            saveHome, allSolPages, openWhySlideModal, saveWhySlide, deleteWhySlide,
            openReviewModal, saveReview, deleteReview,
            addPartnerLogo, deletePartnerLogo, dragLogoIdx, onLogoDragStart, onLogoDragOver, onLogoDrop,
            addAutoImage, deleteAutoImage,
            navLocation, navItems_data, navForm, megaNavItem, megaCategories,
            loadNav, openNavModal, saveNavItem, deleteNavItem,
            allNavSolPages, openMegaEditor, saveMegaCategory, deleteMegaCategory, saveMegaSubitem, deleteMegaSubitem,
            solutionsTab, solutionItems, solutionItemForm,
            solPagesTab, solPagesList, editingSolPage, editingSolBlocks, editingSolPageObj,
            solPageBlocks, solPageForm, loadSolPages, openSolPageEditor, saveSolPage,
            deleteSolPage, openSolBlockEditor, saveSolBlock, reorderSolBlocks,
            loadSolutionItems, openSolutionItemModal, saveSolutionItem, deleteSolutionItem,
            allReviews, allCases, allPosts, loadReviews,
            casesList, editingCase, caseTab, allTerms, caseForm,
            openCaseEditor, saveCase, deleteCase,
            postsList, editingPost, postTab, authorsList, postForm, defaultExtras,
            galleryAddImage, galleryMoveImg, buildGalleryShortcode, buildMediaShortcode, insertShortcode,
            mediaPickerIsMulti, multiSelectedMedia, openMultiPicker, toggleMultiSelect, confirmMultiSelect,
            mediaScSlots, mediaScActiveSlot, mediaScPickSlot, buildMediaShortcodeFromSlots,
            mediaScImg, mediaScVideo, openMediaShortcodeModal, confirmMediaShortcode,
            ctaForm, openCtaModal, buildCtaShortcode, confirmCta,
            quoteForm, openQuoteModal, buildQuoteShortcode, confirmQuote,
            openPostEditor, switchPostTab, savePost, deletePost, backFromPost, copyGalleryShortcode, copyMediaShortcode,
            termForm, openTermModal, saveTerm, deleteTerm,
            authorForm, openAuthorModal, saveAuthor, deleteAuthor,
            mediaList, uploadFiles, uploadAndPick, pickMedia, selectMedia, deleteMedia, copyUrl,
            settingsData, saveSettings,
            onLangChange, slugify, loadSvgInto,
            homeBlocksList, loadBlocks, toggleBlock, moveBlock, saveBlocksOrder,
        };
    }
}).mount('#app');
</script>
</body>
</html>
