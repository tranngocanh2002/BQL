/**
 *
 * TicketDetail
 *
 */

import { Button, Form, Input, Row, Upload } from "antd";
import classnames from "classnames";
import { Bind, Debounce } from "lodash-decorators";
import React from "react";

import Uploader from "../../../components/Uploader";

import "./index.less";

import { CHAT_BOX_TYPE_EXTERNAL } from "./constants";

import { injectIntl } from "react-intl";
import WithRole from "../../../components/WithRole";
import { getFullLinkImage } from "../../../connection";
import MessageLeft from "./MessageLeft";
import MessageRight from "./MessageRight";
import message from "./messages";

/* eslint-disable react/prefer-stateless-function */
@Form.create()
class ChatBox extends React.PureComponent {
  state = {
    attachFile: [],
    content: "",
    sending: false,
  };

  @Bind()
  @Debounce(300)
  resizeInput() {
    if (!this._input) {
      return;
    } else {
      this.setState({
        heightInput: this._input.clientHeight,
      });
    }
  }

  componentDidMount() {
    setTimeout(() => {
      this._scroll.scrollTop = this._scroll.scrollHeight;
    }, 500);
  }

  render() {
    const {
      language,
      heightBlock,
      messages,
      type,
      roles,
      userInfo,
      disableFeedback,
      limitFile = 999,
    } = this.props;
    const formatMessage = this.props.intl.formatMessage;
    const { attachFile, content } = this.state;

    return (
      <div
        className="blockChat"
        style={{
          minHeight: 400,
          height: `calc(100vh - ${heightBlock || 10000}px - 350px)`,
          position: "relative",
        }}
      >
        <div
          ref={(_scroll) => (this._scroll = _scroll)}
          style={{
            overflowY: "scroll",
            height: `calc(100% - 15px - ${this.state.heightInput || 41}px - ${
              attachFile.length != 0 ? 80 : 0
            }px)`,
            marginBottom: 10,
            paddingLeft: 5,
            paddingRight: 5,
          }}
          className="messageList"
        >
          {messages.map((mess, index) => {
            let images = [];
            if (
              !!mess.attach &&
              mess.attach.images.length > 0 &&
              mess.resident_user_id > 0
            ) {
              images = mess.attach.images.map(function (image, index) {
                if (typeof image == "string" || image instanceof String) {
                  return {
                    uid: index,
                    name: index,
                    status: "done",
                    url: getFullLinkImage(image, true),
                  };
                } else {
                  return image;
                }
              });
            }
            if (
              !!mess.attach &&
              mess.attach.images.length > 0 &&
              mess.management_user_id > 0
            ) {
              images = mess.attach.images.map(function (image, index) {
                if (typeof image == "string" || image instanceof String) {
                  return {
                    uid: index,
                    name: index,
                    status: "done",
                    url: getFullLinkImage(image),
                  };
                } else {
                  return image;
                }
              });
            }
            if (type == CHAT_BOX_TYPE_EXTERNAL) {
              if (mess.resident_user_id > 0) {
                let author_name = mess.resident_user_name;
                let author_avatar = mess.resident_user_avatar;
                let author_role = formatMessage(message.resident);
                return (
                  <MessageLeft
                    key={"external_MessageLeft" + index}
                    index={"external_" + index}
                    author_avatar={author_avatar}
                    author_name={author_name}
                    author_role={author_role}
                    images={images}
                    created_at={mess.created_at}
                    content={mess.content}
                  />
                );
              } else if (mess.management_user_id > 0) {
                let author_name =
                  mess.management_user_id != userInfo.id
                    ? mess.management_user_name
                    : "";
                let author_role =
                  mess.management_user_id != userInfo.id
                    ? this.props.language === "en"
                      ? mess.management_user_auth_group_name_en
                      : mess.management_user_auth_group_name
                    : "";
                let author_avatar = mess.management_user_avatar;
                return (
                  <MessageRight
                    key={"external_MessageRight" + index}
                    index={"external_" + index}
                    author_avatar={author_avatar}
                    author_name={author_name}
                    author_role={author_role}
                    images={images}
                    created_at={mess.created_at}
                    content={mess.content}
                  />
                );
              }
            } else {
              if (mess.management_user_id != userInfo.id) {
                let author_name = mess.management_user_name;
                let author_avatar = mess.management_user_avatar;
                return (
                  <MessageLeft
                    key={"external_MessageLeft" + index}
                    index={"external_" + index}
                    author_avatar={author_avatar}
                    author_name={author_name}
                    author_role={
                      language === "vi"
                        ? mess.management_user_auth_group_name
                        : mess.management_user_auth_group_name_en
                    }
                    images={images}
                    created_at={mess.created_at}
                    content={mess.content}
                  />
                );
              } else {
                let author_avatar = mess.management_user_avatar;
                return (
                  <MessageRight
                    key={"external_MessageRight" + index}
                    index={"external_" + index}
                    author_avatar={author_avatar}
                    images={images}
                    created_at={mess.created_at}
                    content={mess.content}
                  />
                );
              }
            }
          })}
        </div>
        {attachFile.length != 0 && (
          <div
            style={{
              // overflowX: "scroll",
              marginLeft: 16,
              marginRight: 16,
            }}
          >
            <Row
              type="flex"
              style={{
                width: attachFile.length * 58,
              }}
            >
              <Upload
                listType="picture-card"
                fileList={attachFile}
                onRemove={(file) => {
                  this.setState((prev) => ({
                    attachFile: prev.attachFile.filter(
                      (fff) => file.uid != fff.uid
                    ),
                  }));
                }}
                onPreview={this.handlePreview}
                onChange={this.handleChange}
                showUploadList={{
                  showRemoveIcon: true,
                  showPreviewIcon: true,
                  showDownloadIcon: false,
                }}
              ></Upload>
              <div
                style={{ position: "relative" }}
                onClick={() =>
                  this.setState({
                    attachFile: [],
                  })
                }
              >
                <span
                  style={{
                    position: "absolute",
                    right: 0,
                    top: -7,
                    cursor: "pointer",
                  }}
                >
                  <i
                    className="fa fa-times-circle"
                    style={{ color: "red" }}
                  ></i>
                </span>
              </div>
              {/* {
							 attachFile.map((file, index) => {
							 return <Row type='flex' align='middle' className='fileAttach' key={`index---${index}`} style={{
							 backgroundImage: `url('${file.url}')`,
							 }}  >
							 </Row>
							 })
							 } */}
            </Row>
          </div>
        )}
        <WithRole roles={roles}>
          <div ref={(_inpt) => (this._input = _inpt)}>
            {!disableFeedback && (
              <Row
                type="flex"
                align="middle"
                justify="space-between"
                style={{ paddingLeft: 16, paddingRight: 16, marginTop: 6 }}
              >
                <Uploader
                  showUploadList={false}
                  accept={"image/*"}
                  disabled={attachFile.length >= limitFile}
                  onUploaded={(file) => {
                    this.setState((prev) => {
                      return {
                        attachFile: prev.attachFile.concat([
                          {
                            uid: Date.now(),
                            name: "xxx.png",
                            status: "done",
                            url: getFullLinkImage(file),
                            url_end: `${file}`,
                          },
                        ]),
                      };
                    });
                  }}
                >
                  <i
                    className={classnames(
                      "material-icons",
                      attachFile.length >= limitFile ? "disabled" : "iconAction"
                    )}
                    style={{ marginTop: 8 }}
                  >
                    attach_file
                  </i>
                </Uploader>
                <Input.TextArea
                  placeholder={formatMessage(message.enterContent)}
                  autosize={{ minRows: 2, maxRows: 2 }}
                  maxLength={160}
                  onChange={(e) => {
                    this.setState({
                      content: e.target.value,
                    });
                    // this.resizeInput()
                  }}
                  value={content}
                  style={{ width: "calc(100% - 100px)" }}
                />
                <Button
                  type="primary"
                  onClick={() => {
                    const { content, attachFile } = this.state;

                    this.setState({
                      sending: true,
                      content: "",
                      attachFile: [],
                    });
                    this.props.handleSendMessage({
                      content: content,
                      attach: {
                        images: attachFile.map(function (file) {
                          return file.url_end;
                        }),
                      },
                    });
                    this.setState({
                      sending: false,
                    });
                  }}
                  disabled={this.state.content.trim() === "" ? true : false}
                >
                  {formatMessage(message.send)}
                </Button>
              </Row>
            )}
          </div>
        </WithRole>
      </div>
    );
  }
}
export default injectIntl(ChatBox);
