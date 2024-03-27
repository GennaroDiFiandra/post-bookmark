"use strict";

document.addEventListener("DOMContentLoaded", () => {
  const button = document.querySelector(".bookmark-me-button");
  if (!button) return;

  button.addEventListener("click", BookmarkMeButtonHandler);
});

const BookmarkMeButtonHandler = {

  handleEvent: async function(event)
  {
    event.preventDefault();

    const initialData = BookmarkMeButtonHandler.getGlobalData(event);
    const preparedData = BookmarkMeButtonHandler.preparePostRequestData(initialData);
    const result = await BookmarkMeButtonHandler.doPostRequest(preparedData);

    result && BookmarkMeButtonHandler.hideBookmarkMeButton(event);
    result && BookmarkMeButtonHandler.showBookmarkedPostsButton();
  },

  getGlobalData: function(event)
  {
    // BookmarkMeButtonData is a global variable
    const {ajaxUrl, nonce} = BookmarkMeButtonData;
    const postId = event.target.dataset.postid;
    const userId = event.target.dataset.userid;

    return { ajaxUrl, nonce, postId, userId }
  },

  preparePostRequestData: function({ ajaxUrl, nonce, postId, userId })
  {
    const url = new URL(ajaxUrl);
    url.searchParams.set("action", "bookmark_me_button");
    url.searchParams.set("nonce", nonce);
    url.searchParams.set("post_id", postId);
    url.searchParams.set("user_id", userId);
    const payload = url.search;
    const headers = new Headers({"Content-Type": "application/json"});

    return { url, headers, payload }
  },

  doPostRequest: async function({ url, headers, payload })
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
      output = true;
    }
    catch (error)
    {
      console.error(error)
      output = false;
    }

    return output;

  },

  hideBookmarkMeButton: function(event)
  {
    event.target.style.display = "none";
  },

  showBookmarkedPostsButton: function()
  {
    const button = document.querySelector(".bookmarked-posts-button");
    if (!button) return;

    if (!button.classList.contains("_active")) button.classList.add("_active");
  },

};