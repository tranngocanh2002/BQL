/**
 *
 * TicketDetail
 *
 */

import { Avatar, Row, Tooltip, Upload } from "antd";
import moment from "moment";
import React from "react";

import { getFullLinkImage } from "../../../connection";

import "./index.less";

import "moment/locale/vi";

/* eslint-disable react/prefer-stateless-function */
export default class MessageLeft extends React.PureComponent {
  render() {
    const {
      index,
      author_avatar,
      images,
      author_name,
      created_at,
      content,
      author_role,
    } = this.props;
    console.log("images", images);
    return (
      <Row key={`index-${index}`} style={{ marginTop: 10 }}>
        <Row type="flex" align="bottom">
          <Avatar src={getFullLinkImage(author_avatar)} icon="user" />
          <div style={{ maxWidth: "70%" }}>
            {!!author_name && (
              <span className="leftTitle">{author_name} | </span>
            )}
            {!!author_role && <span className="leftGroup">{author_role}</span>}
            <Row
              type="flex"
              align="middle"
              style={{
                background: "#EFF1F4",
                minHeight: 32,
                marginLeft: 10,
                borderRadius: "16px 16px 16px 0",
                padding: 10,
              }}
            >
              <span className="contentLeft" style={{ whiteSpace: "pre-wrap" }}>
                {content}
              </span>
            </Row>
            <Row type="flex" align="middle" style={{ marginLeft: "10px" }}>
              <Upload
                listType="picture-card"
                fileList={images}
                onRemove={false}
                onPreview={this.handlePreview}
                onChange={this.handleChange}
                showUploadList={{ showDownloadIcon: false }}
              ></Upload>
            </Row>
            <Tooltip
              title={moment.unix(created_at).format("YYYY-MM-DD HH:mm:ss")}
            >
              <span className="leftDate">
                {moment.unix(created_at).fromNow()}{" "}
              </span>
            </Tooltip>
          </div>
        </Row>
      </Row>
    );
  }
}
