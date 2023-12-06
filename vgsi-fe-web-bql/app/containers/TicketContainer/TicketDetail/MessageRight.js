/**
 *
 * TicketDetail
 *
 */

import { Avatar, Row, Tooltip, Upload } from "antd";
import moment from "moment";
import "moment/locale/vi";
import React from "react";
import { getFullLinkImage } from "../../../connection";
import "./index.less";

/* eslint-disable react/prefer-stateless-function */
export default class MessageRight extends React.PureComponent {
  render() {
    const {
      author_avatar,
      images,
      author_name,
      created_at,
      content,
      author_role,
    } = this.props;
    return (
      <Row style={{ marginTop: 10 }}>
        <Row type="flex" align="bottom" justify={"end"}>
          <div style={{ maxWidth: "70%" }}>
            {!!author_role && <span className="rightGroup">{author_role}</span>}
            {!!author_name && (
              <span className="rightTitle">| {author_name}</span>
            )}
            <Row
              type="flex"
              align="middle"
              style={{
                background: "#1890ff",
                minHeight: 32,
                marginRight: 10,
                borderRadius: "16px 16px 0 16px",
                padding: 10,
              }}
              justify={"end"}
            >
              <span className="contentRight" style={{ whiteSpace: "pre-wrap" }}>
                {content}
              </span>
            </Row>
            <Row
              type="flex"
              align="middle"
              style={{ marginRight: "2px", float: "right" }}
            >
              <Upload
                listType="picture-card"
                fileList={images}
                onRemove={false}
                onPreview={this.handlePreview}
                onChange={this.handleChange}
                showUploadList={{ showDownloadIcon: false }}
              />
            </Row>
            <Tooltip
              title={moment.unix(created_at).format("YYYY-MM-DD HH:mm:ss")}
            >
              <span className="rightDate">
                {moment.unix(created_at).fromNow()}{" "}
              </span>
            </Tooltip>
          </div>
          <Avatar
            style={{ backgroundColor: "#1890ff" }}
            icon="user"
            src={getFullLinkImage(author_avatar)}
          />
        </Row>
      </Row>
    );
  }
}
