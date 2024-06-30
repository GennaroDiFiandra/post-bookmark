"use strict";

document.addEventListener("DOMContentLoaded", () => {
  const list = document.querySelector(".bookmarked-posts-list");
  if (!list) return;

  list.addEventListener("click", BookmarkedPostsListHandler);
});

const BookmarkedPostsListHandler = {

  handleEvent: async function(event)
  {
    if (!event.target.classList.contains("bookmarked-posts-item-del")) return;

    event.preventDefault();

    const initialData = this.getGlobalData(event);
    const preparedData = this.preparePostRequestData(initialData);
    const result = await this.doPostRequest(preparedData);

    result && this.updatePostsList(result);
  },

  getGlobalData: function(event)
  {
    // BookmarkedPostsListData is a global variable
    const {ajaxUrl, nonce} = BookmarkedPostsListData;

    const clicked = event.target;
    const postId = clicked.dataset.postid;
    const userId = clicked.dataset.userid;
    const target = clicked.closest(".bookmarked-posts-item");

    return { ajaxUrl, nonce, postId, userId, target }
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

    let output;

    try
    {
      const response = await fetch(url, options);
      if (!response.ok) throw `Status code ${response.status} - bad response from the server`;
      output = target;
    }
    catch (error)
    {
      console.error(error)
      output = false;
    }

    return output;

  },

  updatePostsList: function(target)
  {
    target.remove();
  },

};