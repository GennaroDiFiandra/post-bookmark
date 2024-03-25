"use strict";

document.addEventListener("DOMContentLoaded", () => {
  const list = document.querySelector(".bookmarked-posts-list");
  if (!list) return;

  list.addEventListener("click", BookmarkedPostsListHandler);
});

const BookmarkedPostsListHandler = {

  handleEvent: async function(event)
  {
    event.preventDefault();

    const input = BookmarkedPostsListHandler.getGlobalData(event);
    const PostRequestData = BookmarkedPostsListHandler.preparePostRequestData(input);
    const target = await BookmarkedPostsListHandler.doPostRequest(PostRequestData);
    BookmarkedPostsListHandler.updatePostsList(target);
  },

  getGlobalData: function(event)
  {
    // BookmarkedPostsListData is a global variable
    const {ajaxUrl, nonce} = BookmarkedPostsListData;

    const clicked = event.target;
    if (clicked.classList.contains("bookmarked-posts-item"))
    {
      const postId = clicked.children[0].dataset.postid;
      const userId = clicked.children[0].dataset.userid;
      const target = clicked;

      return { ajaxUrl, nonce, postId, userId, target }
    }
    else if (clicked.classList.contains("bookmarked-posts-item-ref"))
    {
      const postId = clicked.dataset.postid;
      const userId = clicked.dataset.userid;
      const target = clicked.closest(".bookmarked-posts-item");

      return { ajaxUrl, nonce, postId, userId, target }
    }
  },

  preparePostRequestData: function({ ajaxUrl, nonce, postId, userId, target })
  {
    const url = new URL(ajaxUrl);
    url.searchParams.set("action", "bookmarked_posts_list");
    url.searchParams.set("nonce", nonce);
    url.searchParams.set("post_id", postId);
    url.searchParams.set("user_id", userId);
    const payload = url.search;
    const headers = new Headers({"Content-Type": "application/json"});

    return { url, headers, payload, target }
  },

  doPostRequest: async function({ url, headers, payload, target })
  {
    const options = {
      method: "POST",
      headers: headers,
      credentials: "same-origin",
      body: JSON.stringify(payload),
    };

    const response = await fetch(url, options);
    if (!response.ok) throw new Error(`Status code ${response.status} - bad response from the server`);
    return target;
  },

  updatePostsList: function(target)
  {
    target.remove();
  },

};