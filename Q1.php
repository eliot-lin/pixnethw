<?php

function getUserArticles(int $user_id, int $article_id)
{ 
    if ($user_id && $article_id) {
        return null;
    } elseif (empty($user = User::getUser($user_id))) {
        throw new AlertException("查無此帳號!", '/');
    } elseif (empty($blog = $user->blog)) {
        throw new AlertException("帳號尚未有部落格!", '/');
    } elseif (empty($article = $blog->getArticle($article_id))) {
        throw new AlertException("此帳號無此文章!", '/');
    } else {
        return $article;
    }
}