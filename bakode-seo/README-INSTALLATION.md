# Bakode.cn SEO优化包 - OpenCart 4 安装指南

## 📦 文件清单

| 文件 | 用途 | 安装位置 |
|------|------|----------|
| `baidu-seo-header.twig` | 百度SEO头部代码 | 复制内容到 header.twig |
| `product-schema.twig` | 产品结构化数据 | 复制内容到 product.twig |
| `faq-schema.json` | FAQ结构化数据 | 添加到首页 </body> 前 |
| `organization-schema.json` | 企业结构化数据 | 添加到所有页面 |
| `robots.txt` | 爬虫规则 | 上传到网站根目录 |
| `baidu-sitemap.xml` | 百度专用站点地图 | 上传到网站根目录 |
| `china-speed-optimization.php` | 中国CDN加速 | 参考代码替换 |

---

## 🚀 安装步骤

### 1. 百度SEO头部代码

**位置**: `catalog/view/template/common/header.twig`

在 `<head>` 标签内，`</head>` 之前添加 `baidu-seo-header.twig` 的内容。

**重要**: 将 `YOUR_VERIFICATION_CODE` 替换为你在[百度站长平台](https://ziyuan.baidu.com)获取的验证码。

---

### 2. 产品Schema结构化数据

**位置**: `catalog/view/template/product/product.twig`

在 `</body>` 标签之前添加 `product-schema.twig` 的内容。

---

### 3. FAQ Schema (首页)

**位置**: `catalog/view/template/common/home.twig` 或首页模板

在 `</body>` 之前添加:

```html
<script type="application/ld+json">
{复制 faq-schema.json 的全部内容}
</script>
```

---

### 4. 企业Schema (所有页面)

**位置**: `catalog/view/template/common/footer.twig`

在 `</body>` 之前添加:

```html
<script type="application/ld+json">
{复制 organization-schema.json 的全部内容}
</script>
```

---

### 5. Robots.txt

通过FTP上传 `robots.txt` 到网站根目录，覆盖原文件。

验证: 访问 https://bakode.cn/robots.txt

---

### 6. 百度站点地图

1. 上传 `baidu-sitemap.xml` 到网站根目录
2. 登录[百度站长平台](https://ziyuan.baidu.com)
3. 进入: 网站管理 → 链接提交 → sitemap
4. 提交: https://bakode.cn/baidu-sitemap.xml

---

### 7. 中国CDN加速 (可选)

查找你模板中的外部资源，替换为中国CDN:

| 原地址 | 替换为 |
|--------|--------|
| fonts.googleapis.com | fonts.loli.net |
| code.jquery.com | lib.baomitu.com |
| cdn.jsdelivr.net | cdn.bootcdn.net |
| cdnjs.cloudflare.com | cdn.bootcdn.net |

---

## ✅ 验证清单

安装完成后，使用以下工具验证:

1. **百度站长平台抓取诊断**: https://ziyuan.baidu.com
2. **Schema验证**: https://search.google.com/test/rich-results
3. **移动端适配测试**: 百度站长平台 → 移动适配

---

## 📞 技术支持

如需帮助，请联系开发者或在Emergent平台继续对话。

---

*生成日期: 2026-03-15*
*适用于: OpenCart 4.x + Bakode.cn*
