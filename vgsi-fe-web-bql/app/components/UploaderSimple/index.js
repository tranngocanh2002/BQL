import React from "react";

import { Upload, Icon, message, Spin, Tooltip } from "antd";
import { getHeadersUpload } from "../../connection/fileServer";
import { FormattedMessage, injectIntl } from "react-intl";
import("./index.less");
import messages from "components/messages";

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

class UploaderSimple extends React.Component {
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
    const { intl, maxSize } = this.props;
    const formatMessage = intl.formatMessage;
    function beforeUpload(file) {
      const isLt2M = file.size / 1024 / 1024 < (maxSize || 25);
      if (!isLt2M) {
        message.error(
          formatMessage({ ...messages.imageOverSize }, { size: maxSize || 25 })
        );
      }
      return isLt2M;
    }
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
            <FormattedMessage {...messages.chooseImage} />
          </div>
        )}
      </div>
    );
    // const uploadButton = !imageUrl ? (
    //   <div>
    //     {!this.props.disabled ? (
    //       this.state.loading ? (
    //         <Spin />
    //       ) : (
    //         <Icon type={"plus"} />
    //       )
    //     ) : undefined}
    //     {!this.props.disabled && (
    //       <div className="ant-upload-text">Chọn ảnh</div>
    //     )}
    //   </div>
    // ) : (
    //   <div
    //     style={{
    //       marginTop: 10,
    //     }}
    //   >
    //     {!this.props.disabled ? (
    //       this.state.loading ? (
    //         <Spin />
    //       ) : (
    //         <div className="ant-upload-text">
    //           <Icon type={"upload"} /> Thay ảnh
    //         </div>
    //       )
    //     ) : undefined}
    //   </div>
    // );

    return (
      <div>
        <Tooltip title={formatMessage(messages.chooseImageFromDevice)}>
          <Upload
            disabled={this.props.disabled}
            name={formatMessage(messages.chooseImage)}
            listType="picture-card"
            className="uploader-simple"
            showUploadList={false}
            beforeUpload={this.props.beforeUpload || beforeUpload}
            onChange={this.handleChange}
            accept={this.props.accept || ".png,.jpg,.jpeg,.jfif"}
            {...getHeadersUpload()}
            data={(file) => ({
              "UploadForm[files][]": file,
            })}
          >
            <Tooltip
              title={formatMessage(messages.chooseImageFromDevice)}
              placement="right"
            >
              {/* <div>
                {imageUrl && (
                  <img
                    style={{ width: 280, height: 230 }}
                    src={getFullLinkImage(imageUrl)}
                  />
                )}

                {uploadButton}
              </div> */}
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
            </Tooltip>

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
          </Upload>
        </Tooltip>
      </div>
    );
  }
}

export default injectIntl(UploaderSimple);
