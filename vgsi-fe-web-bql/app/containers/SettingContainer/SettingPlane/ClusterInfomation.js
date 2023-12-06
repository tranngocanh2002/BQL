import React from "react";

import { Button, Col, Form, Modal, Row } from "antd";
import Avatar from "../../../components/Avatar";
import styles from "./index.less";
import messages from "./messages";

import WithRole from "../../../components/WithRole";
import { getFullLinkImage } from "../../../connection";
import config from "../../../utils/config";
import { deleteAreaAction } from "./actions";

const confirm = Modal.confirm;

@Form.create()
export default class ClusterInfomation extends React.PureComponent {
  _onDelete = (title) => {
    let data = this.props.data;
    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.confirmDeleteLevel, {
        title: this.props.intl.formatMessage(messages.info),
      }),
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.cancel),
      onOk: () => {
        this.props.dispatch(
          deleteAreaAction({
            id: data.id,
            message: this.props.intl.formatMessage(messages.deleteLevelSuccess),
          })
        );
      },
      onCancel() {},
    });
  };

  _onSave = () => {
    this.props.onEdit && this.props.onEdit();
  };

  render() {
    const { data } = this.props;
    const { level, name, description, medias, short_name } = data;
    return (
      <>
        <Row type="flex" align="middle" justify="space-between">
          <span className={styles.titleInfoCum}>
            {this.props.intl.formatMessage(messages.informationLevel, {
              cap: name,
            })}
          </span>
          <WithRole
            roles={[config.ALL_ROLE_NAME.SETTING_BUILDING_AREA_CREATE_UPDATE]}
          >
            <div style={{ marginRight: 16 }}>
              <Button type="primary" ghost onClick={this._onSave} icon="edit">
                {this.props.intl.formatMessage(messages.update)}
              </Button>
              <Button
                style={{ marginLeft: 8 }}
                type="danger"
                ghost
                onClick={() => this._onDelete(config.NAME_CLUSTER[level])}
                icon="delete"
              >
                {this.props.intl.formatMessage(messages.delete)}
              </Button>
            </div>
          </WithRole>
        </Row>
        <Row style={{ marginTop: 40 }}>
          <Col span={12}>
            <Row gutter={24} style={{ marginLeft: 0, marginRight: 0 }}>
              <Row type="flex" align="middle">
                <Col style={{ textAlign: "right", paddingRight: 8 }} xl={7}>
                  {`${this.props.intl.formatMessage(messages.nameLevel, {
                    cap: level,
                  })}: `}
                </Col>
                <Col xl={16}>
                  <span className={styles.titleName}>{name}</span>
                </Col>
              </Row>
              <Row style={{ marginTop: 12 }} type="flex" align="middle">
                <Col style={{ textAlign: "right", paddingRight: 8 }} xl={7}>
                  {`${this.props.intl.formatMessage(messages.shortName)}: `}
                </Col>
                <Col xl={16}>
                  <span className={styles.titleName}>{short_name}</span>
                </Col>
              </Row>
              <Row style={{ marginTop: 12 }} type="flex">
                <Col style={{ textAlign: "right", paddingRight: 8 }} xl={7}>
                  {`${this.props.intl.formatMessage(messages.description)}: `}
                </Col>
                <Col xl={16} style={{ whiteSpace: "pre-wrap" }}>
                  <span>{description}</span>
                </Col>
              </Row>
            </Row>
          </Col>
          <Col offset={1} span={10}>
            <Avatar
              disabled
              imageUrl={
                !!medias && !!medias.imageUrl
                  ? getFullLinkImage(medias.imageUrl)
                  : "https://gw.alipayobjects.com/zos/antfincdn/ZHrcdLPrvN/empty.svg"
              }
            />
          </Col>
        </Row>
      </>
    );
  }
}
