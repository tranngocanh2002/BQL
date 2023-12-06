import React from "react";

import { Button, Col, Form, Input, Modal, notification, Row } from "antd";
import Avatar from "../../../components/Avatar";
import { getFullLinkImage } from "../../../connection";
import config from "../../../utils/config";
import { createAreaAction, updateAreaAction } from "./actions";
import styles from "./index.less";
import messages from "./messages";

const formItemLayout = {
  labelCol: {
    xl: { span: 8 },
    lg: { span: 8 },
    md: { span: 24 },
  },
  wrapperCol: {
    xl: { span: 16 },
    lg: { span: 16 },
    md: { span: 24 },
  },
};

const confirm = Modal.confirm;

@Form.create()
export default class ClusterCreate extends React.PureComponent {
  state = {
    imageUrl:
      !!this.props.adding.data && !!this.props.adding.data.medias
        ? this.props.adding.data.medias.imageUrl
        : undefined,
  };

  _onCancel = () => {
    const { parent, level, setState, removeNewNode, getExpandKeys, data } =
      this.props.adding;
    if (data) {
      confirm({
        autoFocusButton: null,
        title: this.props.intl.formatMessage(messages.confirmCancelEdit, {
          cap: data.name || level,
        }),
        okText: this.props.intl.formatMessage(messages.agree),
        okType: "danger",
        cancelText: this.props.intl.formatMessage(messages.close),
        onOk() {
          setState({
            adding: undefined,
          });
        },
        onCancel() {},
      });
      return;
    }

    confirm({
      autoFocusButton: null,
      title: this.props.intl.formatMessage(messages.confirmCancelCreate),
      // content: 'Some descriptions',
      okText: this.props.intl.formatMessage(messages.agree),
      okType: "danger",
      cancelText: this.props.intl.formatMessage(messages.close),
      onOk() {
        setState(
          {
            adding: undefined,
            selectedKeys: [parent.key],
            expandedKeys: getExpandKeys(parent.key),
          },
          () => removeNewNode("new-node", parent.key)
        );
      },
      onCancel() {},
    });
  };

  //Tạo
  _onCreate = () => {
    const { imageUrl } = this.state;
    const { dispatch, form, adding } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      // if (!imageUrl || imageUrl === "") {
      //   notification["error"]({
      //     placement: "bottomRight",
      //     duration: 3,
      //     onClose: () => {},
      //     message: this.props.intl.formatMessage(messages.emptyAvatar),
      //   });
      //   return;
      // }
      const { name, description, short_name } = values;
      const { level } = adding;
      const cap = config.NAME_CLUSTER[adding.level];
      dispatch(
        createAreaAction({
          name,
          description: description || "",
          medias: {
            imageUrl,
          },
          short_name,
          type: level,
          parent_id:
            adding.level == 1 ? undefined : parseInt(adding.parent.key),

          titleSuccess: this.props.intl.formatMessage(messages.createSuccess, {
            cap,
          }),
          callback: this._callbackSuccess.bind(this),
        })
      );
    });
  };

  //Edit
  _onSave = () => {
    const { imageUrl } = this.state;
    const { dispatch, form, adding } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      // if (!imageUrl || imageUrl === "") {
      //   notification["warning"]({
      //     placement: "error",
      //     duration: 3,
      //     onClose: () => {},
      //     message: this.props.intl.formatMessage(messages.emptyAvatar),
      //   });
      //   return;
      // }
      const { name, description, short_name } = values;
      const { level, setState } = adding;
      const cap = config.NAME_CLUSTER[adding.level];
      dispatch(
        updateAreaAction({
          id: adding.data.id,
          medias: {
            imageUrl,
          },
          short_name,
          name,
          description: description || "",
          type: level,
          titleSuccess: this.props.intl.formatMessage(messages.editSuccess),
          callback: () => {
            setState({
              adding: undefined,
            });
          },
        })
      );
    });
  };

  _callbackSuccess = () => {
    const { parent, setState } = this.props.adding;
    setState({
      adding: undefined,
      selectedKeys: [parent.key],
      expandedKeys: [parent.key],
    });
  };

  render() {
    const { adding, settingPlane } = this.props;
    const { getFieldDecorator } = this.props.form;
    const { level, data } = adding;
    const { creatingArea, updatingArea } = settingPlane;
    return (
      <>
        <span className={styles.titleInfoCum}>
          {data
            ? this.props.intl.formatMessage(messages.editLevel, {
                cap: data.name,
              })
            : this.props.intl.formatMessage(messages.addLevel, { cap: level })}
        </span>
        <Row style={{ marginTop: 40 }}>
          <Col span={12}>
            <Row gutter={24} style={{ marginLeft: 0, marginRight: 0 }}>
              <Form {...formItemLayout} onSubmit={this.handleSubmit}>
                <Form.Item
                  label={this.props.intl.formatMessage(messages.nameLevel, {
                    cap: level,
                  })}
                >
                  {getFieldDecorator("name", {
                    initialValue: data ? data.name : "",
                    rules: [
                      {
                        required: true,
                        message: this.props.intl.formatMessage(
                          messages.emptyNameLevel
                        ),
                        whitespace: true,
                      },
                    ],
                  })(<Input style={{ width: "100%" }} maxLength={50} />)}
                </Form.Item>
                <Form.Item
                  label={this.props.intl.formatMessage(messages.shortName)}
                >
                  {getFieldDecorator("short_name", {
                    initialValue: data ? data.short_name : "",
                    rules: [
                      {
                        required: true,
                        message: this.props.intl.formatMessage(
                          messages.emptyShortName
                        ),
                        whitespace: true,
                      },
                    ],
                  })(<Input style={{ width: "100%" }} maxLength={50} />)}
                </Form.Item>
                <Form.Item
                  label={this.props.intl.formatMessage(messages.description)}
                >
                  {getFieldDecorator("description", {
                    initialValue: data ? data.description : "",
                    rules: [
                      {
                        required: true,
                        message: this.props.intl.formatMessage(
                          messages.emptyDescription
                        ),
                        whitespace: true,
                      },
                    ],
                  })(<Input.TextArea rows={4} maxLength={1000} />)}
                </Form.Item>
              </Form>
              <Row gutter={24}>
                <Col xl={8} lg={8} md={24} />
                <Col xl={16} lg={16} md={24}>
                  <Row gutter={24}>
                    <Col span={12} style={{ paddingLeft: 0 }}>
                      <Button
                        block
                        ghost
                        type="danger"
                        onClick={this._onCancel}
                        disabled={creatingArea || updatingArea}
                      >
                        {this.props.intl.formatMessage(messages.cancel)}
                      </Button>
                    </Col>
                    <Col span={12} style={{ paddingLeft: 0 }}>
                      <Button
                        block
                        ghost
                        type="primary"
                        onClick={data ? this._onSave : this._onCreate}
                        loading={creatingArea || updatingArea}
                      >
                        {this.props.intl.formatMessage(messages.save)}
                      </Button>
                    </Col>
                  </Row>
                </Col>
              </Row>
            </Row>
          </Col>
          <Col offset={1} span={10}>
            <Avatar
              imageUrl={getFullLinkImage(this.state.imageUrl)}
              onUploaded={(url) => {
                // console.log(`url`, url)
                this.setState({
                  imageUrl: url,
                });
              }}
            />
          </Col>
        </Row>
      </>
    );
  }
}
