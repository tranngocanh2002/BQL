import { Button, Col, Icon, Input, Upload, notification } from "antd";
import React from "react";
import { injectIntl } from "react-intl";
import styles from "./index.less";
import { getHeadersUpload } from "connection/fileServer";
import messages from "../messages";
import { getFullLinkImage } from "connection";
import { connect } from "react-redux";
import { createTaskCommentAction } from "./actions";

class TaskComment extends React.PureComponent {
  state = {
    attachFile: "",
    type: "",
    content: "",
  };

  beforeUpload = (file) => {
    let { formatMessage } = this.props.intl;

    if (
      !file.type.includes("image/") &&
      !file.type.includes("video/") &&
      !file.type.includes("pdf") &&
      !file.type.includes("doc") &&
      !file.type.includes("xls")
    ) {
      notification.error({
        message: formatMessage(messages.fileIncorrect),
      });
      return false;
    }
    if (file.type.includes("image")) {
      if (file.size > 10000000) {
        notification.error({
          message: formatMessage(messages.maxSizeImg),
        });
        return false;
      }
    }
    if (file.size > 20000000) {
      notification.error({
        message: formatMessage(messages.maxSizeDoc),
      });
      return false;
    }
    return true;
  };

  handleChange = (uploadData) => {
    if (uploadData.file.status === "done") {
      let type = "";
      if (uploadData.file.type.includes("image")) {
        type = "image";
      } else if (uploadData.file.type.includes("video")) {
        type = "video";
      } else {
        type = "document";
      }

      this.setState({
        type: type,
        attachFile: uploadData.file.response.data.files
          ? uploadData.file.response.data.files[0]
          : "",
      });
    }
  };

  onPressEnter = () => {
    const { task_id } = this.props;
    const { attachFile, type, content } = this.state;

    if ((attachFile && type) || content) {
      let medias = {};
      let data = {
        job_id: task_id,
      };
      if (attachFile && type) {
        medias[type] = attachFile;
        data.medias = medias;
      }
      if (content) {
        data.content = content;
      }
      this.props.dispatch(createTaskCommentAction(data));
      this.setState({
        attachFile: "",
        type: "",
        content: "",
      });
    }
  };

  onChangeText = (e) => {
    this.setState({ content: e.target.value });
  };

  removeAttach = () => {
    this.setState({
      type: "",
      attachFile: "",
    });
  };

  render() {
    const { type, content, attachFile } = this.state;
    let { formatMessage } = this.props.intl;
    console.log("props", this.props);
    return (
      <Col className={styles.comment}>
        {type === "image" && attachFile && (
          <div
            style={{
              margin: 8,
              marginTop: 20,
            }}
          >
            <a
              href={getFullLinkImage(attachFile)}
              target="_blank"
              key={`cmt-img`}
            >
              <img
                src={getFullLinkImage(attachFile)}
                style={{
                  width: 40,
                  height: 40,
                }}
              />
            </a>
            <Button
              onClick={this.removeAttach}
              type="primary"
              shape="circle"
              icon="close"
              style={{
                width: 16,
                height: 16,
                fontSize: 10,
                position: "absolute",
                left: 44,
                top: 14,
              }}
            />
          </div>
        )}
        {type === "video" && attachFile && (
          <div
            style={{
              margin: 8,
              marginTop: 20,
            }}
          >
            <a
              href={getFullLinkImage(attachFile)}
              target="_blank"
              key={`cmt-video`}
            >
              <video controls={false} width="80" autoPlay={false}>
                <source src={getFullLinkImage(attachFile)} type="video/mp4" />
              </video>
            </a>
            <Button
              onClick={this.removeAttach}
              type="primary"
              shape="circle"
              icon="close"
              size={10}
              style={{
                width: 16,
                height: 16,
                fontSize: 10,
                position: "absolute",
                left: 85,
                top: 14,
              }}
            />
          </div>
        )}
        {type === "document" && attachFile && (
          <div
            style={{
              margin: 8,
              marginTop: 20,
            }}
          >
            <a
              href={getFullLinkImage(attachFile)}
              target="_blank"
              key={`cmt-doc`}
            >
              <div
                style={{
                  width: 40,
                  height: 40,
                  backgroundColor: "#D9D9D9",
                  borderRadius: 2,
                  display: "flex",
                  justifyContent: "center",
                  alignItems: "center",
                }}
              >
                {attachFile.split("/").pop().split(".").pop()}
              </div>
            </a>
            <Button
              onClick={this.removeAttach}
              type="primary"
              shape="circle"
              icon="close"
              size={10}
              style={{
                width: 16,
                height: 16,
                fontSize: 10,
                position: "absolute",
                left: 44,
                top: 14,
              }}
            />
          </div>
        )}
        <Input
          placeholder={formatMessage(messages.plhComment)}
          style={{
            borderWidth: 0,
          }}
          maxLength={160}
          onPressEnter={this.onPressEnter}
          value={content}
          onChange={this.onChangeText}
          suffix={
            <Upload
              disabled={!!type && !!attachFile}
              showUploadList={false}
              accept="image/*, .pdf, .xls, .xlsx, .doc, .docx., video/*"
              data={(file) => ({
                "UploadForm[files][]": file,
              })}
              {...getHeadersUpload()}
              beforeUpload={this.beforeUpload}
              onChange={this.handleChange}
            >
              <Icon type="upload" style={{ color: "rgba(0,0,0,.45)" }} />
            </Upload>
          }
        />
      </Col>
    );
  }
}

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

export default connect(null, mapDispatchToProps)(injectIntl(TaskComment));
