import React from "react";

import { Upload, Icon, message, Spin, Button } from "antd";
import { getHeadersUpload } from "../../connection/fileServer";
import("./index.less");
import $ from "jquery";
import { FormattedMessage, injectIntl } from "react-intl";
import messages from "containers/AccountContainer/AccountBase/messages";

// function getBase64(img, callback) {
//   const reader = new FileReader();
//   reader.addEventListener("load", () => callback(reader.result));
//   reader.readAsDataURL(img);
// }

// function beforeUpload(file) {
//   const isImage = file.type.startsWith("image/");
//   if (!isImage) {
//     message.error("Bạn chỉ có thể tải lên ảnh!");
//   }
//   const isLt2M = file.size / 1024 / 1024 < 25;
//   if (!isLt2M) {
//     message.error("Ảnh tải lên vượt quá 25MB");
//   }
//   return isImage && isLt2M;
// }

export class Avatar extends React.Component {
  state = {
    loading: false,
    fileList: [],
    imageUrl: this.props.imageUrl,
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.imageUrl != nextProps.imageUrl) {
      this.setState({
        imageUrl: nextProps.imageUrl,
      });
    }
  }

  handleChange = (info) => {
    if (info.file.status === "uploading") {
      this.setState({ loading: true });
      return;
    }
    if (info.file.status === "done") {
      // Get this url from response in real world.
      this.setState({
        loading: false,
      });
      const { success, data } = info.file.response;
      if (success && !!data && !!data.files && data.files.length > 0) {
        this.props.onUploaded && this.props.onUploaded(data.files[0]);
      }
    }

    this.setState({
      fileList: info.fileList,
    });
  };

  render() {
    const { intl } = this.props;
    const onlyUploadImageText = intl.formatMessage({
      ...messages.onlyUploadImage,
    });
    const imageTooLargeText = intl.formatMessage({
      ...messages.imageTooLarge,
    });
    const notFormatImageText = intl.formatMessage({
      ...messages.notFormatImage,
    });
    const imageUrl = this.state.imageUrl;

    const uploadButton = (
      <div
        style={{
          position: "absolute",
          top: "50%",
          left: "50%",
          transform: "translate(-50%, -50%)",
        }}
      >
        {!this.props.disabled && !imageUrl ? (
          this.state.loading ? (
            <Spin />
          ) : (
            <Icon type={"plus"} />
          )
        ) : undefined}
        {!this.props.disabled && !imageUrl && (
          <div className="ant-upload-text">
            <FormattedMessage {...messages.avatar} />
          </div>
        )}
      </div>
    );

    return (
      <div>
        <Upload
          disabled={this.props.disabled}
          name="Ảnh đại diện"
          listType="picture-card"
          className="avatar-uploader"
          showUploadList={false}
          beforeUpload={(file) => {
            const isLt2M = file.size / 1024 / 1024 < 10;
            if (!isLt2M) {
              message.error(imageTooLargeText);
            }
            return isLt2M;
          }}
          onChange={this.handleChange}
          accept={".png,.jpg,.jpeg,.jfif"}
          {...getHeadersUpload()}
          data={(file) => ({
            "UploadForm[files][]": file,
          })}
        >
          <div
            style={{
              width: "100%",
              paddingTop: "100%",
              position: "relative",
              overflow: "hidden",
              backgroundImage: `url(${imageUrl})`,
              backgroundSize: "100%",
              backgroundPosition: "center",
            }}
          >
            {uploadButton}
          </div>
          {!!imageUrl && !this.props.disabled && this.props.allowClear && (
            <i
              className="material-icons"
              style={{
                position: "absolute",
                top: 10,
                right: 10,
                fontSize: 36,
              }}
              onClick={(e) => {
                e.stopPropagation();
                e.preventDefault();
                this.props.onUploaded && this.props.onUploaded();
              }}
            >
              close
            </i>
          )}
          {!!imageUrl && !this.props.disabled && (
            <Button
              onClick={() => {
                // $("span.ant-upload").find("input").click();
                this.props.onUploaded && this.props.onUploaded();
              }}
              style={{
                position: "absolute",
                left: "50%",
                transform: "translate(-50%, 50%)",
              }}
            >
              <FormattedMessage {...messages.changeAvatar} />
            </Button>
          )}
        </Upload>
      </div>
    );
  }
}

export default injectIntl(Avatar);
