import React from "react";
import { Editor } from "react-draft-wysiwyg";
import "react-draft-wysiwyg/dist/react-draft-wysiwyg.css";
import styles from "./Editor.less";
import { CUSTOM_TOOLBAR, editorLabelsVN } from "utils/config";
import { compose } from "redux";
import { createStructuredSelector } from "reselect";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { connect } from "react-redux";
import { EditorState, Modifier } from "draft-js";
import htmlToDraft from "html-to-draftjs";
import { List } from "immutable";

const handlePastedText = (text, html, editorState, onChange) => {
  // const selectedBlock = getSelectedBlock(editorState);
  // if (selectedBlock && selectedBlock.type === "code") {
  //   const contentState = Modifier.replaceText(
  //     editorState.getCurrentContent(),
  //     editorState.getSelection(),
  //     text,
  //     editorState.getCurrentInlineStyle()
  //   );
  //   onChange(EditorState.push(editorState, contentState, "insert-characters"));
  //   return true;
  // } else
  if (html) {
    // clean up html and only keep the <body> content
    const body = html
      .replace(/<!--.*?-->/gs, "")
      .match(/<body[^>]*>(.*?)<\/body>/s)[1]
      // remove all the /n and /br from the html that is not behind a tag like </p>\n or <p>\br replace(/(?<!<\/[a-z])\n/g, "");
      .replace(/(?<!<\/?\w+>)\s*\n\s*/g, " ");

    const contentBlock = htmlToDraft(body);
    let contentState = editorState.getCurrentContent();
    contentBlock.entityMap.forEach((value, key) => {
      contentState = contentState.mergeEntityData(key, value);
    });
    contentState = Modifier.replaceWithFragment(
      contentState,
      editorState.getSelection(),
      new List(contentBlock.contentBlocks)
    );
    onChange(EditorState.push(editorState, contentState, "insert-characters"));
    return true;
  }
  return false;
};

const DraftEditor = (props) => {
  const { language } = props;
  return (
    <Editor
      toolbarClassName={styles.toolbar}
      handlePastedText={handlePastedText}
      wrapperClassName="demo-wrapper"
      editorClassName="rdw-storybook-editor"
      toolbar={CUSTOM_TOOLBAR}
      localization={{
        locale: language,
        translations: language === "vi" ? editorLabelsVN : undefined,
      }}
      {...props}
    />
  );
};

const mapStateToProps = createStructuredSelector({
  language: makeSelectLocale(),
});

const withConnect = connect(mapStateToProps);
export default compose(withConnect)(DraftEditor);
