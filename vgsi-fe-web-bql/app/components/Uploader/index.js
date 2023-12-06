import React from "react";

import { Upload, message } from "antd";
import { getHeadersUpload } from "../../connection/fileServer";
import "./index.less";
import { getFullLinkImage } from "../../connection";

function beforeUpload(file, accept) {
  if (accept) {
    const isImage = accept.some(
      (accc) =>
        (file.type.length > 0 && file.type.startsWith(accc)) ||
        file.name.toLowerCase().endsWith(accc.toLowerCase())
    );
    if (!isImage) {
      message.error("Tệp tải lên không đúng định dạng.");
      return { error: "Tệp tải lên không đúng định dạng." };
    }
  }
  const isLt2M = file.size / 1024 / 1024 < 10;
  if (!isLt2M) {
    message.error("Tệp tải lên vượt quá 10MB");
    return { error: "Tệp tải lên vượt quá 10MB" };
  }
  if (!!file.url || !accept) return { success: true };
  return { success: true };
}

class Uploader extends React.Component {
  state = {
    fileList: (this.props.fileList || []).map((file, index) => {
      return {
        ...file,
        url: getFullLinkImage(file.url),
        url_end: file.url,
      };
    }),
  };

  handleChange = (info) => {
    let valid = this.props.notCheck
      ? { success: true }
      : beforeUpload(info.file, this.props.acceptList);
    if (valid.success) {
      let fileList = info.fileList.map((file) => {
        if (file.response && file.response.success) {
          // Component will show file.url as link
          file.url = getFullLinkImage(file.response.data.files[0]);
          file.url_end = file.response.data.files[0];
        }
        return file;
      });
      this.setState({ fileList });
    } else {
      !!this.props.onErrorCallback && this.props.onErrorCallback(valid.error);
      return;
    }
    if (info.file.status === "uploading") {
      this.setState({ loading: true });
    }
    if (info.file.status === "done") {
      this.setState({ loading: false });
      const { success, data } = info.file.response;
      if (success && !!data && !!data.files && data.files.length > 0) {
        this.props.onUploaded &&
          this.props.onUploaded(data.files[0], info.file);
      }
    }
  };

  render() {
    const { fileList } = this.state;
    const { children, disabled, ...rest } = this.props;
    return (
      <Upload
        showUploadList={false}
        {...rest}
        disabled={this.state.loading || disabled}
        onChange={this.handleChange}
        fileList={fileList}
        {...getHeadersUpload()}
        data={(file) => ({
          "UploadForm[files][]": file,
        })}
      >
        {children}
      </Upload>
    );
  }
}

export default Uploader;
