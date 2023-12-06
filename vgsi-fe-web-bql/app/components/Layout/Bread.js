import {
  Breadcrumb,
  Card,
  Col,
  Form,
  Icon,
  Input,
  Modal,
  Row,
  Select,
  Tooltip,
  notification,
} from "antd";
import PropTypes from "prop-types";
import React, { PureComponent } from "react";
import { Redirect } from "react-router";

import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { changeTaskStatusAction } from "containers/TaskContainer/TaskDetail/actions";
import makeSelectTaskDetail from "containers/TaskContainer/TaskDetail/selector";
import { injectIntl } from "react-intl";
import { connect } from "react-redux";
import { withRouter } from "react-router";
import { Link } from "react-router-dom";
import { selectAuthGroup, selectUserDetail } from "redux/selectors";
import { createStructuredSelector } from "reselect";
import { JobStatuses } from "utils/constants";
import {
  addLangPrefix,
  notificationBar,
  pathMatchRegexp,
  queryAncestors,
} from "../../utils";
import styles from "./Bread.less";
import messages from "./messages";
import config from "utils/config";
import makeSelectCombineCardDetail from "containers/CombineCardContainer/CombineCardDetail/selectors";
import {
  changeCombineCardStatusAction,
  createActiveCard,
  fetchDetailCombineCardAction,
  updateCombineCardAction,
} from "containers/CombineCardContainer/CombineCardDetail/actions";
import ModalEditCard from "containers/CombineCardContainer/CombineCardDetail/ModalEditCard";
import ModalActiveCard from "containers/CombineCardContainer/CombineCardDetail/ModalActiveCard";
const CollectionCreateForm = Form.create({ name: "form_in_modal" })(
  // eslint-disable-next-line
  class extends React.Component {
    render() {
      const { visible, onCancel, onDecline, form, intl } = this.props;
      const { getFieldDecorator } = form;
      const reasonPlaceholderText = intl.formatMessage({
        ...messages.reasonPlaceholder,
      });
      return (
        <Modal
          visible={visible}
          title={intl.formatMessage(messages.cancelCardQuestion)}
          okText={intl.formatMessage(messages.agree)}
          okType="danger"
          cancelText={intl.formatMessage(messages.cancel)}
          onCancel={onCancel}
          onOk={onDecline}
          width={"666px"}
          bodyStyle={{ paddingBottom: 0 }}
        >
          <Form>
            <Form.Item>
              {getFieldDecorator("reason", {
                rules: [
                  {
                    required: true,
                    message: intl.formatMessage(messages.reasonRequest),
                  },
                ],
              })(
                <Input.TextArea
                  //style={{ minHeight: "150" }}
                  placeholder={reasonPlaceholderText}
                  maxLength={200}
                />
              )}
            </Form.Item>
          </Form>
        </Modal>
      );
    }
  }
);
class Bread extends PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      taskAction: undefined,
      cardAction: undefined,
      visibleAddCard: false,
      visibleAddCard2: false,
      currentEdit: undefined,
      success: false,
      visible: false,
    };
  }
  generateBreadcrumbs = (paths) =>
    paths.map((item, key) => {
      return (
        <Breadcrumb.Item key={key}>
          {paths.length - 1 !== key && key != 0 ? (
            <Link to={addLangPrefix(item.route) || "#"}>
              {this.props.language == "vi"
                ? item.name
                : item.name_en
                ? item.name_en
                : item.name}
            </Link>
          ) : (
            <span style={{ fontWeight: "400", color: "black" }}>
              {this.props.language == "vi"
                ? item.name
                : item.name_en
                ? item.name_en
                : item.name}
            </span>
          )}
        </Breadcrumb.Item>
      );
    });

  onSelectTaskAction = (action) => {
    const formatMessage = this.props.intl.formatMessage;

    if (action === "cancel") {
      Modal.confirm({
        autoFocusButton: null,
        title: formatMessage(messages.cancelTaskQuestion),
        //content: formatMessage(messages.cancelTaskQuestion),
        cancelText: formatMessage(messages.cancel),
        okText: formatMessage(messages.agree),
        okType: "danger",
        onOk: () => {
          const { detail } = this.props.taskDetail;
          this.props.dispatch(
            changeTaskStatusAction({
              id: detail.data.id,
              status: JobStatuses.cancel,
            })
          );
        },
      });
    } else if (action === "delete") {
      Modal.confirm({
        autoFocusButton: null,

        title: formatMessage(messages.deleteTaskQuestion),
        // content: formatMessage(messages.deleteTaskQuestion),
        cancelText: formatMessage(messages.cancel),
        okText: formatMessage(messages.agree),
        okType: "danger",
        onOk: () => {
          const { detail } = this.props.taskDetail;
          window.connection.deleteTask({ id: detail.data.id }).then((res) => {
            if (res.success) {
              notification.success({
                message: formatMessage(messages.deleteTaskSuccess),
                placement: "bottomRight",
              });
              this.props.history.push("/main/task/list");
            }
          });
        },
      });
    } else if (action === "edit") {
      const { detail } = this.props.taskDetail;
      const record = detail.data;
      this.props.history.push(`/main/task/edit/${record.id}`, { record });
    }
  };
  onSelectCardAction = (action) => {
    const formatMessage = this.props.intl.formatMessage;

    if (action === "cancel") {
      Modal.confirm({
        autoFocusButton: null,
        title: formatMessage(messages.cancelCardQuestion),
        cancelText: formatMessage(messages.cancel),
        okText: formatMessage(messages.agree),
        okType: "danger",
        onOk: () => {
          this.setState({ visible: true });
        },
      });
    } else if (action === "delete") {
      Modal.confirm({
        autoFocusButton: null,
        title: formatMessage(messages.deleteCardQuestion),
        cancelText: formatMessage(messages.cancel),
        okText: formatMessage(messages.agree),
        okType: "danger",
        onOk: () => {
          const { detail } = this.props.cardDetail;
          window.connection
            .deleteCombineCard({ id: detail.data.id })
            .then((res) => {
              if (res.success) {
                notification.success({
                  message: formatMessage(messages.deleteCardSuccess),
                  placement: "bottomRight",
                });
                this.props.history.push("/main/merge-card/list");
              }
            });
        },
      });
    } else if (action === "recall") {
      Modal.confirm({
        autoFocusButton: null,
        title: formatMessage(messages.takeCardQuestion),
        cancelText: formatMessage(messages.cancel),
        okText: formatMessage(messages.agree),
        okType: "danger",
        onOk: () => {
          const { detail } = this.props.cardDetail;
          this.props.dispatch(
            changeCombineCardStatusAction({
              id: detail.data.id,
              status: 3,
            })
          );
        },
      });
    } else if (action === "lock") {
      Modal.confirm({
        autoFocusButton: null,
        title: "Khóa thẻ",
        cancelText: formatMessage(messages.cancel),
        okText: formatMessage(messages.agree),
        okType: "danger",
        onOk: () => {
          const { detail } = this.props.cardDetail;
          this.props.dispatch(
            changeCombineCardStatusAction({
              id: detail.data.id,
              status: 2,
            })
          );
        },
      });
    } else if (action === "active") {
      this.setState({ visibleAddCard2: true });
    } else if (action === "edit") {
      this.setState({ visibleAddCard: true });
    }
  };
  handleCancel2 = async (values) => {
    const { detail } = this.props.cardDetail;
    const { form } = this.formRef.props;
    try {
      let res = await window.connection.changeCombineCardStatus({
        id: detail.data.id,
        reason: values.reason,
        status: 4,
      });
      if (res.success) {
        notificationBar(
          this.props.intl.formatMessage(messages.cancelCardSuccess)
        );
        this.setState({
          visible: false,
        });
        this.props.dispatch(
          fetchDetailCombineCardAction({
            id: detail.data.id,
          })
        );
        form.resetFields();
      }
    } catch (error) {
      console.log(error);
    }
  };
  closeModal = () => {
    this.setState({
      visible: false,
    });
  };
  saveFormRef = (formRef) => {
    this.formRef = formRef;
  };
  handleCancel = () => {
    const { form } = this.formRef.props;
    form.validateFields((err, values) => {
      if (err) {
        return;
      }
      this.handleCancel2(values);
    });
  };
  render() {
    const {
      routeList,
      location,
      language,
      taskDetail,
      cardDetail,
      userDetail,
      auth_group,
      intl,
    } = this.props;
    const { detail } = taskDetail;
    const formatMessage = this.props.intl.formatMessage;
    const showDelete = detail.data.status === JobStatuses.new;
    const showEditCancel =
      detail.data.status === JobStatuses.new ||
      detail.data.status === JobStatuses.doing;
    let currentRouteActive = null;
    // Find a route that matches the pathname.
    const currentRoute = routeList.find((_) => {
      if (_.children) {
        currentRouteActive = _.children.find((item) => {
          return item.route && pathMatchRegexp(item.route, location.pathname);
        });
        return currentRouteActive;
      } else {
        return _.route && pathMatchRegexp(_.route, location.pathname);
      }
    });
    if (this.state.success == true) {
      // this.props.history.push(`/main/merge-card/detail/${cardDetail.detail.data.id}`,
      // {
      //   record: {
      //     ...cardDetail.detail.data,
      //   },
      // });
      this.setState({ success: false });

      return (
        <Redirect
          to={
            (`/main/merge-card/detail/${cardDetail.detail.data.id}`,
            {
              record: {
                ...cardDetail.detail.data,
              },
            })
          }
        />
      );
    }
    // Find the breadcrumb navigation of the current route match and all its ancestors.
    let paths = currentRoute
      ? queryAncestors(routeList, currentRoute, "breadcrumbParentId").reverse()
      : [
          routeList[0],
          {
            id: 404,
            name: "Not Found",
          },
        ];

    if (!currentRoute) {
      return (
        <Row
          style={{
            paddingLeft: 24,
            height: 84,
            backgroundColor: "#fff",
            zIndex: 5,
          }}
          className={styles.bread}
          type="flex"
          align="middle"
        >
          <Col />
        </Row>
      );
    }
    if (currentRoute.children) {
      paths = [...paths, currentRouteActive];
    }
    return (
      <Row
        style={{
          paddingLeft: 24,
          height: 84,
          backgroundColor: "#fff",
          zIndex: 5,
        }}
        className={styles.bread}
        type="flex"
        align="middle"
        justify="space-between"
      >
        <Col>
          <Breadcrumb style={{ marginBottom: 4 }}>
            {this.generateBreadcrumbs(paths)}
          </Breadcrumb>
          {!!currentRouteActive && currentRouteActive ? (
            <span
              style={{ color: "#1B1B27", fontWeight: "bold", fontSize: 20 }}
            >
              {(language === "vi"
                ? currentRouteActive.description
                : currentRouteActive.description_en
                ? currentRouteActive.description_en
                : currentRouteActive.description) || <br />}{" "}
              {!!currentRouteActive.titleTooltip && (
                <Tooltip
                  title={
                    language === "vi"
                      ? currentRouteActive.titleTooltip
                      : currentRouteActive.titleTooltip_en
                      ? currentRouteActive.titleTooltip_en
                      : currentRouteActive.titleTooltip
                  }
                >
                  <Icon
                    type="info-circle"
                    style={{ color: "#595959", fontSize: 16, marginLeft: 8 }}
                  />
                </Tooltip>
              )}
            </span>
          ) : (
            <span
              style={{ color: "#1B1B27", fontWeight: "bold", fontSize: 20 }}
            >
              {(!!currentRoute &&
                (language === "vi"
                  ? currentRoute.description
                  : currentRoute.description_en
                  ? currentRoute.description_en
                  : currentRoute.description)) || <br />}{" "}
              {!!currentRoute && !!currentRoute.titleTooltip && (
                <Tooltip
                  title={
                    language === "vi"
                      ? currentRoute.titleTooltip
                      : currentRoute.titleTooltip_en
                      ? currentRoute.titleTooltip_en
                      : currentRoute.titleTooltip
                  }
                >
                  <Icon
                    type="info-circle"
                    style={{ color: "#595959", fontSize: 16, marginLeft: 8 }}
                  />
                </Tooltip>
              )}
            </span>
          )}
        </Col>
        {location.pathname &&
          location.pathname.includes("task/detail") &&
          detail.data &&
          detail.data.assignor &&
          userDetail &&
          detail.data.assignor.id === userDetail.id &&
          detail.data.status !== JobStatuses.done &&
          detail.data.status !== JobStatuses.cancel &&
          auth_group.checkRole([
            config.ALL_ROLE_NAME.WORKFLOW_MANAGENMENT_GROUP,
          ]) && (
            <Col
              style={{
                marginRight: 20,
              }}
            >
              <Select
                placeholder={formatMessage(messages.optional)}
                style={{
                  width: 180,
                  padding: 12,
                }}
                size="large"
                onSelect={this.onSelectTaskAction}
                value={this.state.taskAction}
              >
                {showEditCancel && (
                  <Select.Option value="edit">
                    {formatMessage(messages.editTask)}
                  </Select.Option>
                )}
                {showEditCancel && (
                  <Select.Option value="cancel">
                    {formatMessage(messages.cancelTask)}
                  </Select.Option>
                )}
                {showDelete && (
                  <Select.Option value="delete">
                    {formatMessage(messages.deleteTask)}
                  </Select.Option>
                )}
              </Select>
            </Col>
          )}
        {location.pathname &&
          location.pathname.includes("merge-card/detail") &&
          cardDetail &&
          cardDetail.detail &&
          cardDetail.detail.data &&
          auth_group.checkRole([
            config.ALL_ROLE_NAME.CARD_MANAGEMENT_DETAIL,
          ]) && (
            <Col
              style={{
                marginRight: 20,
              }}
            >
              <Select
                placeholder={formatMessage(messages.optional)}
                style={{
                  width: 180,
                  padding: 12,
                }}
                size="large"
                onSelect={this.onSelectCardAction}
                value={this.state.cardAction}
              >
                {
                  <Select.Option value="edit">
                    {formatMessage(messages.editCard)}
                  </Select.Option>
                }
                {cardDetail.detail.data.status == 0 && (
                  <Select.Option value="active">{"Kích hoạt"}</Select.Option>
                )}

                {cardDetail.detail.data.status == 1 && (
                  <Select.Option value="recall">
                    {formatMessage(messages.takeCard)}
                  </Select.Option>
                )}
                {
                  <Select.Option value="cancel">
                    {formatMessage(messages.cancelCard)}
                  </Select.Option>
                }
                {cardDetail.detail.data.status == 0 && (
                  <Select.Option value="delete">
                    {formatMessage(messages.deleteCard)}
                  </Select.Option>
                )}
                {<Select.Option value="lock">{"Khóa"}</Select.Option>}
              </Select>
            </Col>
          )}
        {cardDetail && cardDetail.detail && cardDetail.detail.data && (
          <ModalEditCard
            visible={this.state.visibleAddCard}
            setState={this.setState.bind(this)}
            currentEdit={cardDetail.detail.data}
            handlerUpdate={(values) => {
              this.props.dispatch(
                updateCombineCardAction({
                  ...values,
                  id: cardDetail.detail.data.id,
                  callback: () => {
                    this.setState({ visibleAddCard: false });
                    this.props.dispatch(
                      fetchDetailCombineCardAction({
                        id: cardDetail.detail.data.id,
                      })
                    );
                  },
                })
              );
            }}
          />
        )}

        {cardDetail && cardDetail.detail && cardDetail.detail.data && (
          <ModalActiveCard
            visible={this.state.visibleAddCard2}
            setState={this.setState.bind(this)}
            currentEdit={cardDetail.detail.data}
            creating={cardDetail.updating}
            cardDetail={cardDetail}
            dispatch={this.props.dispatch}
            handlerUpdate={(values) => {
              this.props.dispatch(
                createActiveCard({
                  ...values,
                  id: cardDetail.detail.data.id,
                  status: 1,
                  callback: () => {
                    this.setState({ visibleAddCard2: false });
                    this.props.dispatch(
                      fetchDetailCombineCardAction({
                        id: cardDetail.detail.data.id,
                      })
                    );
                  },
                })
              );
            }}
          />
        )}

        {cardDetail && cardDetail.detail && cardDetail.detail.data && (
          <CollectionCreateForm
            intl={intl}
            wrappedComponentRef={this.saveFormRef}
            visible={this.state.visible}
            onCancel={this.closeModal}
            onDecline={this.handleCancel}
          />
        )}
      </Row>
    );
  }
}

Bread.propTypes = {
  routeList: PropTypes.array,
};

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const mapStateToProps = createStructuredSelector({
  language: makeSelectLocale(),
  taskDetail: makeSelectTaskDetail(),
  userDetail: selectUserDetail(),
  auth_group: selectAuthGroup(),
  cardDetail: makeSelectCombineCardDetail(),
});

export default withRouter(
  connect(mapStateToProps, mapDispatchToProps)(injectIntl(Bread))
);
