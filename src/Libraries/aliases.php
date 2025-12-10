<?php
namespace Tuezy\Libraries;

if (class_exists(__NAMESPACE__.'\\Functions')) {
    \class_alias(__NAMESPACE__.'\\Functions', '\\Functions');
}
if (class_exists(__NAMESPACE__.'\\Email')) {
    \class_alias(__NAMESPACE__.'\\Email', '\\Email');
}
if (class_exists(__NAMESPACE__.'\\Seo')) {
    \class_alias(__NAMESPACE__.'\\Seo', '\\Seo');
}
if (class_exists(__NAMESPACE__.'\\Cache')) {
    \class_alias(__NAMESPACE__.'\\Cache', '\\Cache');
}
if (class_exists(__NAMESPACE__.'\\Cart')) {
    \class_alias(__NAMESPACE__.'\\Cart', '\\Cart');
}
if (class_exists(__NAMESPACE__.'\\BreadCrumbs')) {
    \class_alias(__NAMESPACE__.'\\BreadCrumbs', '\\BreadCrumbs');
}
if (class_exists(__NAMESPACE__.'\\Statistic')) {
    \class_alias(__NAMESPACE__.'\\Statistic', '\\Statistic');
}
