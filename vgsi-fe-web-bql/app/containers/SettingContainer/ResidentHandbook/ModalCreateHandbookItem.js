import { Form, Input, InputNumber, Modal, TreeSelect } from "antd";
import _ from "lodash";
import React from "react";

import("./index.less");

import { ContentState, EditorState, convertToRaw } from "draft-js";
import draftToHtml from "draftjs-to-html";
import htmlToDraft from "html-to-draftjs";

import DraftEditor from "components/Editor/Editor";
import { injectIntl } from "react-intl";
import { CUSTOM_TOOLBAR } from "../../../utils/config";
import messages from "../messages";

import("./index.less");
const TreeNode = TreeSelect.TreeNode;
const formItemLayout = {
  labelCol: {
    span: 4,
  },
  wrapperCol: {
    span: 18,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ModalCreateHandbookItem extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {
      editorState: EditorState.createEmpty(),
    };
  }

  handlerUpdate = () => {
    const { currentEdit, form, intl } = this.props;
    const { validateFieldsAndScroll, setFields } = form;

    let contentRaw = convertToRaw(this.state.editorState.getCurrentContent());
    let isErrorContent = false;
    if (
      !contentRaw ||
      !contentRaw.blocks ||
      !contentRaw.blocks.some(
        (block) => block.text.replace(/ /g, "").length != 0
      )
    ) {
      setFields({
        content: {
          value: "",
          errors: [
            new Error(intl.formatMessage({ ...messages.contentRequired })),
          ],
        },
      });
      isErrorContent = true;
    } else {
      setFields({
        content: {
          value: "111",
        },
      });
      isErrorContent = false;
    }

    validateFieldsAndScroll((errors, values) => {
      if (errors || isErrorContent) {
        return;
      }
      if (currentEdit) {
        this.props.handlerUpdateHandbookItem &&
          this.props.handlerUpdateHandbookItem({
            ...values,
            id: currentEdit.id,
            content: draftToHtml(contentRaw),
            order: values.order,
          });
      } else {
        this.props.handlerAddHandbookItem &&
          this.props.handlerAddHandbookItem({
            ...values,
            content: draftToHtml(contentRaw),
            order: values.order,
          });
      }
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
      if (nextProps.visible && !!nextProps.currentEdit) {
        let blockArray = htmlToDraft(nextProps.currentEdit.content);

        this.setState({
          editorState:
            !!blockArray && !!blockArray.contentBlocks
              ? EditorState.createWithContent(
                  ContentState.createFromBlockArray(blockArray.contentBlocks)
                )
              : EditorState.createEmpty(),
        });
      } else {
        this.setState({
          editorState: EditorState.createEmpty(),
        });
      }
    }
  }

  onEditorStateChange = (editorState) => {
    if (
      draftToHtml(convertToRaw(editorState.getCurrentContent())).includes(
        "https://drive.google.com/file/d/"
      )
    ) {
      let currentContent = draftToHtml(
        convertToRaw(editorState.getCurrentContent())
      )
        .replaceAll(
          "https://drive.google.com/file/d/",
          "https://drive.google.com/uc?export=view&id="
        )
        .replaceAll("/view?usp=sharing", "")
        .replaceAll("/view?usp=drive_link", "");
      let blockArray = htmlToDraft(currentContent);
      this.setState({
        editorState: EditorState.createWithContent(
          ContentState.createFromBlockArray(blockArray.contentBlocks)
        ),
      });
    } else {
      this.setState({
        editorState,
      });
    }
  };

  render() {
    const { creating, visible, setState, currentEdit } = this.props;
    console.log("currentEdit", currentEdit);
    const { getFieldDecorator, getFieldsError } = this.props.form;
    const { editorState } = this.state;
    const errorCurrent = getFieldsError(["content"]);
    const formatMessage = this.props.intl.formatMessage;

    return (
      <Modal
        title={
          currentEdit
            ? formatMessage(messages.edit)
            : formatMessage(messages.create)
        }
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={() => {
          if (creating) return;
          setState({
            visibleModalItem: false,
          });
        }}
        okText={
          currentEdit
            ? formatMessage(messages.update)
            : formatMessage(messages.add)
        }
        cancelText={formatMessage(messages.cancel)}
        okButtonProps={{ loading: creating }}
        cancelButtonProps={{ disabled: creating }}
        maskClosable={false}
        width={"70%"}
      >
        <Form
          {...formItemLayout}
          className="residentHandbookPage"
          labelAlign="left"
        >
          <Form.Item label={formatMessage(messages.title)} colon={false}>
            {getFieldDecorator("title", {
              initialValue: currentEdit && currentEdit.title,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.emptyTitle),
                  whitespace: true,
                },
              ],
            })(<Input maxLength={255} />)}
          </Form.Item>
          <Form.Item
            label={`${formatMessage(messages.title)} (EN)`}
            colon={false}
          >
            {getFieldDecorator("title_en", {
              initialValue: currentEdit && currentEdit.title_en,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.emptyTitleEn),
                  whitespace: true,
                },
              ],
            })(<Input maxLength={255} />)}
          </Form.Item>
          <Form.Item label={formatMessage(messages.content)} colon={false}>
            {getFieldDecorator("content", {
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.emptyContent),
                },
              ],
            })(
              <div
                style={{
                  border: errorCurrent.content ? "1px solid red" : "",
                }}
              >
                <DraftEditor
                  editorState={editorState}
                  wrapperClassName="demo-wrapper"
                  editorClassName="rdw-storybook-editor"
                  onEditorStateChange={this.onEditorStateChange}
                  handleBeforeInput={() => {
                    if (
                      _.sum(
                        convertToRaw(
                          this.state.editorState.getCurrentContent()
                        ).blocks.map((bl) => bl.text.length)
                      ) >= 5000
                    ) {
                      return "handled";
                    }
                  }}
                  toolbar={{ CUSTOM_TOOLBAR }}
                />
              </div>
            )}
          </Form.Item>
          <Form.Item label={formatMessage(messages.order)} colon={false}>
            {getFieldDecorator("order", {
              initialValue: currentEdit && currentEdit.order,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.emptyOrder),
                },
              ],
            })(<InputNumber maxLength={5} />)}
          </Form.Item>
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalCreateHandbookItem);
