import React from "react";
import { Page } from "components";
import styles from "./index.less";
import makeSelectTaskDetail from "./selector";
import { connect } from "react-redux";
import injectReducer from "utils/injectReducer";
import injectSaga from "utils/injectSaga";
import reducer from "./reducer";
import saga from "./saga";
import { compose } from "redux";
import { injectIntl } from "react-intl";
import {
  changeTaskStatusAction,
  defaultAction,
  fetchDetailTaskAction,
  fetchTaskCommentsAction,
} from "./actions";
import { createStructuredSelector } from "reselect";
import {
  Avatar,
  Button,
  Col,
  Collapse,
  Empty,
  Icon,
  Input,
  List,
  Row,
  Select,
  Tooltip,
  Typography,
  Upload,
} from "antd";
import messages from "../messages";
import { getFullLinkImage } from "connection";
import { Exception } from "ant-design-pro";
import moment from "moment";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { JobStatuses, getTaskStatusName } from "utils/constants";
import { selectAuthGroup, selectUserDetail } from "redux/selectors";
import Comment from "./Comment";
import config from "utils/config";
const { Title, Paragraph } = Typography;
const { Panel } = Collapse;

export class TaskDetail extends React.PureComponent {
  constructor(props) {
    super(props);

    this.state = {};
  }

  componentWillUnmount() {
    this.props.dispatch(defaultAction());
  }

  componentDidMount() {
    const { params } = this.props.match;
    this.props.dispatch(fetchDetailTaskAction({ id: params.id }));
    this.props.dispatch(fetchTaskCommentsAction({ job_id: params.id }));
  }

  renderStatusOption = (status) => {
    if (typeof status === "undefined") {
      return null;
    }

    let { formatMessage } = this.props.intl;
    return (
      <Row type="flex" align="middle">
        {status === JobStatuses.done ? (
          <Icon
            type="check-circle"
            style={{ color: "#1FB266", fontSize: 20 }}
            theme="filled"
          />
        ) : status === JobStatuses.cancel ? (
          <Icon
            type="close-circle"
            style={{ color: "#FF1A12", fontSize: 20 }}
            theme="filled"
          />
        ) : (
          <img
            src={require("../../../images/circle.svg")}
            style={{
              width: 20,
              height: 20,
            }}
          />
        )}
        <Typography
          className={styles.info}
          style={{
            marginLeft: 12,
            marginRight: 12,
          }}
        >
          {formatMessage(messages[getTaskStatusName(status)])}
        </Typography>
      </Row>
    );
  };

  onChangeStatus = (value) => {
    const { params } = this.props.match;
    this.props.dispatch(
      changeTaskStatusAction({
        id: params.id,
        status: value,
      })
    );
  };

  render() {
    let { formatMessage } = this.props.intl;
    const { taskDetail, language, userDetail, auth_group } = this.props;
    const { detail, comments, activities } = taskDetail;

    if (detail.data == -1) {
      return (
        <Page inner>
          <Exception
            type="404"
            desc={25656562}
            actions={
              <Button
                type="primary"
                onClick={() => this.props.history.push("/main/task/list")}
              >
                {formatMessage(messages.back)}
              </Button>
            }
          />
        </Page>
      );
    }

    let listStatus = [];
    if (
      typeof detail.data.status !== "undefined" &&
      typeof detail.data.performers !== "undefined"
    ) {
      if (
        detail.data.status === JobStatuses.new &&
        detail.data.performers.some((p) => p.id === userDetail.id)
      ) {
        listStatus = [JobStatuses.doing, JobStatuses.done];
      }
      if (
        detail.data.status === JobStatuses.doing &&
        detail.data.performers.some((p) => p.id === userDetail.id)
      ) {
        listStatus = [JobStatuses.done];
      }
      if (
        (detail.data.status === JobStatuses.new ||
          detail.data.status === JobStatuses.doing) &&
        detail.data.assignor.id === userDetail.id
      ) {
        listStatus = [JobStatuses.done];
      }
    }

    return (
      <Page loading={detail.loading} inner={detail.loading}>
        <div className={styles.taskDetail}>
          <Row>
            <Col
              span={16}
              style={{ marginRight: 12 }}
              className={styles.taskDetailCard}
            >
              <Title level={3}>{detail.data.title}</Title>

              {auth_group.checkRole([
                config.ALL_ROLE_NAME.WORKFLOW_MANAGENMENT_GROUP,
              ]) && (
                <Select
                  style={{
                    width: 180,
                    padding: 12,
                  }}
                  size="large"
                  placeholder={this.renderStatusOption(detail.data.status)}
                  onChange={this.onChangeStatus}
                  disabled={listStatus.length === 0}
                >
                  {listStatus.map((status) => (
                    <Select.Option value={status} key={`status-${status}`}>
                      {this.renderStatusOption(status)}
                    </Select.Option>
                  ))}
                </Select>
              )}

              <Typography
                className={styles.infoTitle}
                style={{
                  marginTop: 12,
                }}
              >
                {formatMessage(messages.description).toUpperCase()}
              </Typography>

              <Paragraph className={styles.info}>
                {detail.data.description}
              </Paragraph>

              <Typography className={styles.infoTitle}>
                {formatMessage(messages.attachments).toUpperCase()}
              </Typography>

              {detail.data.medias &&
                detail.data.medias.documents &&
                detail.data.medias.documents.length > 0 && (
                  <Col style={{ marginTop: 8 }}>
                    <Upload
                      style={{ width: "auto" }}
                      fileList={
                        detail &&
                        detail.data &&
                        detail.data.medias &&
                        detail.data.medias.documents &&
                        detail.data.medias.documents.map((doc, index) => {
                          return {
                            uid: index,
                            name: doc.split("/").pop(),
                            status: "done",
                            url: getFullLinkImage(doc),
                          };
                        })
                      }
                      onRemove={false}
                      showUploadList={{
                        showDownloadIcon: false,
                        showRemoveIcon: false,
                      }}
                    />
                  </Col>
                )}
              {detail.data.medias &&
                detail.data.medias.images &&
                detail.data.medias.images.length > 0 && (
                  <Row style={{ marginTop: 8 }}>
                    {detail.data.medias.images.map((image, index) => (
                      <a
                        href={getFullLinkImage(image)}
                        target="_blank"
                        rel="noopener noreferrer"
                        key={`images-${index}`}
                      >
                        <Avatar
                          src={getFullLinkImage(image)}
                          shape="square"
                          size={120}
                          style={{
                            marginRight: 12,
                            marginBottom: 12,
                          }}
                        />
                      </a>
                    ))}
                  </Row>
                )}
              {detail.data.medias &&
                detail.data.medias.videos &&
                detail.data.medias.videos.length > 0 && (
                  <Row style={{ marginTop: 8 }}>
                    {detail.data.medias.videos.map((video, index) => (
                      <video
                        controls
                        width="160"
                        key={`videos-${index}`}
                        style={{
                          marginRight: 12,
                          marginBottom: 12,
                        }}
                      >
                        <source
                          src={getFullLinkImage(video)}
                          type="video/mp4"
                        />
                      </video>
                    ))}
                  </Row>
                )}

              <Col>
                <Typography
                  className={styles.infoTitle}
                  style={{ marginTop: 16 }}
                >
                  {formatMessage(messages.comment) +
                    " " +
                    `(${comments.length})`}
                </Typography>

                <Comment task_id={detail.data.id} />

                {comments.length > 0 ? (
                  comments
                    .sort((a, b) => b.created_at - a.created_at)
                    .map((item) => (
                      <Row
                        key={`comments-${item.id}`}
                        style={{
                          marginBottom: 12,
                        }}
                      >
                        <Row type="flex" align="middle">
                          <Avatar
                            src={getFullLinkImage(item.creator.avatar)}
                            icon="user"
                            style={{ marginRight: 8 }}
                          />
                          <Col span={20}>
                            <Row type="flex" justify="space-between">
                              <Typography
                                className={styles.info}
                                style={{
                                  fontSize: 16,
                                  fontWeight: "bold",
                                }}
                              >
                                {item.creator.first_name}
                              </Typography>
                              <Typography className={styles.subInfo}>
                                {moment
                                  .unix(item.created_at)
                                  .format("HH:mm, DD/MM/YYYY")}
                              </Typography>
                            </Row>
                            <Typography className={styles.info}>
                              {language === "en" && item.content_en
                                ? item.content_en
                                : item.content}
                            </Typography>
                          </Col>
                        </Row>
                        {item.medias && (
                          <Col
                            style={{
                              marginTop: 8,
                              marginBottom: 8,
                              marginLeft: 40,
                            }}
                          >
                            {item.medias.image && (
                              <a
                                href={getFullLinkImage(item.medias.image)}
                                target="_blank"
                                rel="noopener noreferrer"
                                key={`cmt-${item.id}-img`}
                              >
                                <img
                                  src={getFullLinkImage(item.medias.image)}
                                  style={{
                                    width: 40,
                                    height: 40,
                                  }}
                                />
                              </a>
                            )}
                            {item.medias.video && (
                              <a
                                href={getFullLinkImage(item.medias.video)}
                                target="_blank"
                                rel="noopener noreferrer"
                                key={`cmt-${item.id}-video`}
                              >
                                <video
                                  controls={false}
                                  width="80"
                                  autoPlay={false}
                                >
                                  <source
                                    src={getFullLinkImage(item.medias.video)}
                                    type="video/mp4"
                                  />
                                </video>
                              </a>
                            )}
                            {item.medias.document && (
                              <div
                                style={{
                                  display: "flex",
                                  flexDirection: "row",
                                  alignItems: "center",
                                }}
                              >
                                <Icon type="paper-clip" />
                                <a
                                  href={getFullLinkImage(item.medias.document)}
                                  target="_blank"
                                  rel="noopener noreferrer"
                                  key={`cmt-${item.id}doc`}
                                  style={{
                                    marginLeft: 4,
                                  }}
                                >
                                  {item.medias.document.split("/").pop()}
                                </a>
                              </div>
                            )}
                          </Col>
                        )}
                      </Row>
                    ))
                ) : (
                  <Col>
                    <Typography className={styles.empty}>
                      {formatMessage(messages.noComment)}
                    </Typography>
                  </Col>
                )}
              </Col>
            </Col>
            <Col span={7}>
              <Col className={styles.taskDetailCard}>
                <Typography className={styles.infoTitle}>
                  {formatMessage(messages.creator).toUpperCase()}
                </Typography>
                {detail.data && detail.data.assignor && (
                  <Row
                    type="flex"
                    align="middle"
                    style={{
                      marginTop: 4,
                    }}
                  >
                    <Avatar
                      src={getFullLinkImage(detail.data.assignor.avatar)}
                      icon="user"
                    />

                    <Col style={{ marginLeft: 12 }}>
                      <Typography className={styles.info}>
                        {detail.data.assignor.first_name}
                      </Typography>
                      <Typography className={styles.info}>
                        {detail.data.assignor.email}
                      </Typography>
                    </Col>
                  </Row>
                )}
                <Typography
                  className={styles.infoTitle}
                  style={{ marginTop: 12, marginBottom: 4 }}
                >
                  {formatMessage(messages.assignees).toUpperCase() +
                    ` (${
                      detail.data.performers && detail.data.performers.length
                        ? detail.data.performers.length
                        : 0
                    })`}
                </Typography>
                {detail.data.performers &&
                  detail.data.performers.length > 0 && (
                    <Row>
                      {detail.data.performers.map((e) => (
                        <Tooltip
                          key={`performers-${e.id}`}
                          title={e.first_name}
                        >
                          <Avatar
                            src={getFullLinkImage(e.avatar)}
                            icon="user"
                            style={{
                              marginRight: 4,
                              marginBottom: 4,
                            }}
                          />
                        </Tooltip>
                      ))}
                    </Row>
                  )}
                <Typography
                  className={styles.infoTitle}
                  style={{ marginTop: 12 }}
                >
                  {formatMessage(messages.time).toUpperCase()}
                </Typography>
                {detail.data.created_at && (
                  <Typography
                    style={{ marginTop: 8 }}
                    className={styles.info}
                  >{`${formatMessage(messages.createTime)}: ${moment
                    .unix(detail.data.created_at)
                    .format("HH:mm, DD/MM/YYYY")}`}</Typography>
                )}
                {detail.data.updated_at_by_user && (
                  <Typography
                    style={{ marginTop: 8 }}
                    className={styles.info}
                  >{`${formatMessage(messages.updateTime)}: ${moment
                    .unix(detail.data.updated_at)
                    .format("HH:mm, DD/MM/YYYY")}`}</Typography>
                )}
                {/* {!detail.data.updated_at_by_user && (
                  <Typography
                    style={{ marginTop: 8 }}
                    className={styles.info}
                  >{`${formatMessage(messages.updateTime)}: ${moment
                    .unix(detail.data.updated_at)
                    .format("HH:mm, DD/MM/YYYY")}`}</Typography>
                )} */}
                {detail.data.time_start && (
                  <Typography
                    style={{ marginTop: 8 }}
                    className={styles.info}
                  >{`${formatMessage(messages.startTime)}: ${moment
                    .unix(detail.data.time_start)
                    .format("HH:mm, DD/MM/YYYY")}`}</Typography>
                )}
                {detail.data.time_end && (
                  <Typography
                    style={{ marginTop: 8 }}
                    className={styles.info}
                  >{`${formatMessage(messages.endTime)}: ${moment
                    .unix(detail.data.time_end)
                    .format("HH:mm, DD/MM/YYYY")}`}</Typography>
                )}
                <Row type="flex" align="bottom" style={{ marginTop: 16 }}>
                  <Typography
                    className={styles.infoTitle}
                    style={{
                      marginRight: 4,
                    }}
                  >
                    {`${formatMessage(messages.priority).toUpperCase()}:`}
                  </Typography>
                  <Typography className={styles.info}>
                    {detail.data && detail.data.prioritize
                      ? formatMessage(messages.yes)
                      : formatMessage(messages.no)}
                  </Typography>
                </Row>
                <Typography
                  className={styles.infoTitle}
                  style={{
                    marginTop: 12,
                    marginBottom: 8,
                  }}
                >
                  {formatMessage(messages.followers).toUpperCase() +
                    ` (${
                      detail.data.people_involveds
                        ? detail.data.people_involveds.length
                        : 0
                    })`}
                </Typography>
                {detail.data && detail.data.people_involveds && (
                  <Row>
                    {detail.data.people_involveds.map((e) => (
                      <Tooltip
                        key={`people_involveds-${e.id}`}
                        title={e.first_name}
                      >
                        <Avatar
                          src={getFullLinkImage(e.avatar)}
                          icon="user"
                          style={{
                            marginRight: 4,
                            marginBottom: 4,
                          }}
                        />
                      </Tooltip>
                    ))}
                  </Row>
                )}
              </Col>
              <Collapse
                style={{
                  marginTop: 12,
                  backgroundColor: "#fff",
                  borderWidth: 0,
                }}
                expandIcon={({ isActive }) => (
                  <Icon type="down" rotate={isActive ? 180 : 0} />
                )}
                expandIconPosition="right"
              >
                <Panel
                  header={
                    <Typography className={styles.infoTitle}>
                      {formatMessage(messages.activity)}
                    </Typography>
                  }
                  style={{
                    borderBottomWidth: 0,
                  }}
                >
                  {activities.length > 0 ? (
                    activities
                      .sort((a, b) => b.created_at - a.created_at)
                      .map((item) => (
                        <Row
                          key={`activities-${item.id}`}
                          type="flex"
                          align="middle"
                          style={{
                            marginBottom: 12,
                          }}
                        >
                          <Avatar
                            src={getFullLinkImage(item.creator.avatar)}
                            icon="user"
                            style={{ marginRight: 8 }}
                          />
                          <Col span={20}>
                            <Row type="flex" justify="space-between">
                              <Typography
                                className={styles.info}
                                style={{
                                  fontSize: 16,
                                  fontWeight: "bold",
                                }}
                              >
                                {item.creator.first_name}
                              </Typography>
                              <Typography className={styles.subInfo}>
                                {moment
                                  .unix(item.created_at)
                                  .format("HH:mm, DD/MM/YYYY")}
                              </Typography>
                            </Row>
                            <Typography className={styles.info}>
                              {language === "en" && item.content_en
                                ? item.content_en
                                : item.content}
                            </Typography>
                          </Col>
                        </Row>
                      ))
                  ) : (
                    <Col>
                      <Typography className={styles.empty}>
                        {formatMessage(messages.noActivity)}
                      </Typography>
                    </Col>
                  )}
                </Panel>
              </Collapse>
            </Col>
          </Row>
        </div>
      </Page>
    );
  }
}

const mapStateToProps = createStructuredSelector({
  taskDetail: makeSelectTaskDetail(),
  language: makeSelectLocale(),
  userDetail: selectUserDetail(),
  auth_group: selectAuthGroup(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

const withReducer = injectReducer({ key: "taskDetail", reducer });
const withSaga = injectSaga({ key: "taskDetail", saga });

export default compose(
  withReducer,
  withSaga,
  withConnect
)(injectIntl(TaskDetail));
