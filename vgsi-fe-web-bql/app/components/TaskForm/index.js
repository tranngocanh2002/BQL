/* eslint-disable react/jsx-no-target-blank */
import React from "react";
import {
  Row,
  Form,
  Input,
  Col,
  Select,
  notification,
  Upload,
  Button,
  DatePicker,
  TimePicker,
  Icon,
  Modal,
  Spin,
} from "antd";
import moment from "moment";
import { getHeadersUpload } from "connection/fileServer";
import { getFullLinkImage } from "connection";
import messages from "../../containers/TaskContainer/messages";
import "./index.less";
import { toLowerCaseNonAccentVietnamese } from "utils";
const { TextArea } = Input;

const formColLayout = {
  xs: 24,
  sm: 24,
  md: 24,
  lg: 12,
  xl: 12,
  xxl: 12,
};

class TaskForm extends React.Component {
  constructor(props) {
    super(props);
    this.selectorRef = React.createRef(null);
    this.state = {
      images: [],
      videos: [],
      documents: [],
      widthItem: 0,
    };
  }

  componentDidMount() {
    const width = this.selectorRef.current.offsetWidth;
    this.setState({
      widthItem: width,
    });
  }
  UNSAFE_componentWillReceiveProps(nextProps) {
    const { medias } = nextProps.record || {};
    if (medias && medias.images) {
      this.setState({
        images: medias.images,
      });
    }
    if (medias && medias.videos) {
      this.setState({
        videos: medias.videos,
      });
    }
    if (medias && medias.documents) {
      this.setState({
        documents: medias.documents,
      });
    }
  }

  beforeUpload = (file) => {
    const { formatMessage } = this.props;
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
      if (uploadData.file.type.includes("image")) {
        this.setState({
          images: [
            ...this.state.images,
            uploadData.file.response.data.files
              ? uploadData.file.response.data.files[0]
              : "",
          ],
        });
      } else if (uploadData.file.type.includes("video")) {
        this.setState({
          videos: [
            ...this.state.videos,
            uploadData.file.response.data.files
              ? uploadData.file.response.data.files[0]
              : "",
          ],
        });
      } else {
        this.setState({
          documents: [
            ...this.state.documents,
            uploadData.file.response.data.files
              ? uploadData.file.response.data.files[0]
              : "",
          ],
        });
      }
    }
  };

  removeAttach = (type, attachFile) => {
    this.setState({
      [type]: this.state[type].filter((att) => att !== attachFile),
    });
  };

  handerCancel = () => {
    this.props.history.push("/main/task/list");
  };

  onSave = () => {
    const {
      form: { validateFieldsAndScroll, setFields },
      handleSubmit,
      formatMessage,
      record,
    } = this.props;

    const { videos, images, documents } = this.state;
    validateFieldsAndScroll((errors, values) => {
      const isTooLate =
        values.time_start && values.time_start.unix() < moment().unix();
      const isStartWrongTime =
        !!values.time_start &&
        !!values.time_end &&
        moment(values.time_start).unix() > moment(values.time_end).unix();
      if (isTooLate) {
        setFields({
          time_start: {
            value: values.time_start,
            errors: [new Error(formatMessage(messages.startTimeValidate))],
          },
        });
      } else {
        setFields({
          time_start: {
            value: values.time_start,
          },
        });
      }
      if (isStartWrongTime) {
        setFields({
          time_end: {
            value: values.time_end,
            errors: [new Error(formatMessage(messages.conditionDate))],
          },
        });
      } else {
        setFields({
          time_end: {
            value: values.time_end,
          },
        });
        if (errors || isTooLate || isStartWrongTime) {
          return;
        }
        handleSubmit({
          ...values,
          time_end: moment(values.time_end).unix() || null,
          time_start: moment(values.time_start).unix() || null,
          medias: {
            videos,
            images,
            documents,
          },
          ...(record && record.id ? { id: record.id } : null),
        });
      }
    });
  };

  render() {
    const {
      formatMessage,
      isEdit = false,
      form,
      staffList,
      loadingStaff,
      record,
      multiple,
    } = this.props;
    const { getFieldDecorator } = form;
    const { videos, images, documents, widthItem } = this.state;

    return (
      <div>
        <div className="title-task">{formatMessage(messages.taskInfo)}</div>
        <Row gutter={24} style={{ marginTop: 40 }}>
          <Col span={24}>
            <Form layout="vertical" onSubmit={() => {}}>
              <Col {...formColLayout}>
                <Form.Item label={formatMessage(messages.title)}>
                  {getFieldDecorator("title", {
                    initialValue: record ? record.title : "",
                    rules: [
                      {
                        required: true,
                        message: formatMessage(messages.itemCantBlankType, {
                          item: formatMessage(messages.title),
                        }),
                        whitespace: true,
                      },
                    ],
                  })(<Input style={{ width: "100%" }} maxLength={255} />)}
                </Form.Item>
                <Form.Item label={formatMessage(messages.description)}>
                  {getFieldDecorator("description", {
                    initialValue: record ? record.description : "",
                  })(<TextArea style={{ height: 124 }} />)}
                </Form.Item>
                <Form.Item label={formatMessage(messages.attachments)}>
                  {getFieldDecorator("medias")(
                    <div
                      ref={this.selectorRef}
                      className="d-flex row-item"
                      style={{ cursor: "pointer" }}
                    >
                      {documents.length + images.length + videos.length <
                        10 && (
                        <Upload
                          showUploadList={false}
                          multiple={multiple}
                          accept="image/*, .pdf, .xls, .xlsx, .doc, .docx, video/*"
                          data={(file) => ({
                            "UploadForm[files][]": file,
                          })}
                          {...getHeadersUpload()}
                          beforeUpload={this.beforeUpload}
                          onChange={this.handleChange}
                        >
                          <div className="center-item col-item size-task-btn-upload upload-task-btn">
                            <Icon type="plus" />
                            <div className="upload-text">
                              {formatMessage(messages.upload)}
                            </div>
                          </div>
                        </Upload>
                      )}
                      {images.map((image) => (
                        <div
                          key={image}
                          className="size-task-btn-upload p-relative ml-20"
                        >
                          <a href={getFullLinkImage(image)} target="_blank">
                            <img
                              src={getFullLinkImage(image)}
                              className="size-task-btn-upload"
                            />
                          </a>
                          <Button
                            onClick={() => this.removeAttach("images", image)}
                            type="primary"
                            shape="circle"
                            icon="close"
                            style={{
                              width: 24,
                              height: 24,
                              fontSize: 10,
                              position: "absolute",
                              right: -10,
                              top: -10,
                            }}
                          />
                        </div>
                      ))}
                      {videos.map((video) => (
                        <div
                          key={video}
                          className="size-task-btn-upload p-relative ml-20"
                        >
                          <a href={getFullLinkImage(video)} target="_blank">
                            <video
                              controls={false}
                              width={100}
                              height={100}
                              autoPlay={false}
                            >
                              <source
                                src={getFullLinkImage(video)}
                                type="video/mp4"
                              />
                            </video>
                          </a>
                          <Button
                            onClick={() => this.removeAttach("videos", video)}
                            type="primary"
                            shape="circle"
                            icon="close"
                            style={{
                              width: 24,
                              height: 24,
                              fontSize: 10,
                              position: "absolute",
                              right: -10,
                              top: -10,
                            }}
                          />
                        </div>
                      ))}
                      {documents.map((doc) => (
                        <div key={doc} className="mtb-20 p-relative">
                          <a href={getFullLinkImage(doc)} target="_blank">
                            <div
                              style={{
                                width: widthItem,
                                height: 40,
                                backgroundColor: "#FFFFFF",
                                borderRadius: 4,
                                border: "1px solid #d9d9d9",
                                padding: "0px 10px",
                                display: "flex",
                                alignItems: "center",
                              }}
                            >
                              {doc.split("/").pop()}
                            </div>
                          </a>
                          <Button
                            onClick={() => this.removeAttach("documents", doc)}
                            type="primary"
                            shape="circle"
                            icon="close"
                            style={{
                              width: 24,
                              height: 24,
                              fontSize: 10,
                              position: "absolute",
                              right: 10,
                              top: 8,
                            }}
                          />
                        </div>
                      ))}
                    </div>
                  )}
                </Form.Item>
              </Col>

              <Col {...formColLayout}>
                <Spin size="large" spinning={loadingStaff}>
                  <Form.Item label={formatMessage(messages.assignee)}>
                    {getFieldDecorator("performer", {
                      initialValue:
                        record && record.performer ? record.performer : [],
                    })(
                      <Select
                        mode="multiple"
                        // loading={loadingStaff}
                        showSearch
                        showArrow
                        placeholder={
                          <React.Fragment>
                            {formatMessage(messages.choseType)}{" "}
                            {formatMessage(messages.assignee).toLowerCase()}{" "}
                          </React.Fragment>
                        }
                        optionFilterProp="children"
                        filterOption={(input, option) =>
                          toLowerCaseNonAccentVietnamese(
                            option.props.children
                          ).indexOf(toLowerCaseNonAccentVietnamese(input)) >= 0
                        }
                      >
                        {staffList.map((staff) => {
                          return (
                            <Select.Option key={staff.id} value={staff.id}>
                              {staff.first_name || ""}
                            </Select.Option>
                          );
                        })}
                      </Select>
                    )}
                  </Form.Item>
                </Spin>

                <Form.Item label={formatMessage(messages.startTime)}>
                  {getFieldDecorator("time_start", {
                    initialValue:
                      record && record.time_start
                        ? moment.unix(record.time_start)
                        : null,
                  })(
                    <DatePicker
                      showTime
                      placeholder={`${formatMessage(
                        messages.choseType
                      )} ${formatMessage(messages.startTime).toLowerCase()}`}
                      style={{ width: "100%" }}
                      locale={this.props.language === "vi" ? "vi" : "en"}
                      format="HH:mm, DD/MM/YYYY"
                      disabledDate={(current) => {
                        return current && current < moment().startOf("day");
                      }}
                    />
                  )}
                </Form.Item>
                <Form.Item label={formatMessage(messages.endTime)}>
                  {getFieldDecorator("time_end", {
                    initialValue:
                      record && record.time_end
                        ? moment.unix(record.time_end)
                        : null,
                  })(
                    <DatePicker
                      showTime
                      placeholder={`${formatMessage(
                        messages.choseType
                      )} ${formatMessage(messages.endTime).toLowerCase()}`}
                      style={{ width: "100%" }}
                      format="HH:mm, DD/MM/YYYY"
                      disabledDate={(current) => {
                        return current && current < moment().startOf("day");
                      }}
                    />
                  )}
                </Form.Item>
                <Spin size="large" spinning={loadingStaff}>
                  <Form.Item label={formatMessage(messages.followers)}>
                    {getFieldDecorator("people_involved", {
                      initialValue:
                        record && record.people_involved
                          ? record.people_involved
                          : [],
                      // rules: [
                      //   {
                      //     required: false,
                      //     message: formatMessage(messages.itemCantBlankType, {
                      //       item: formatMessage(messages.followers),
                      //     }),
                      //   },
                      // ],
                    })(
                      <Select
                        mode="multiple"
                        showArrow
                        // suffixIcon={
                        //   <Icon
                        //     type="info-circle"
                        //     style={{ color: "rgba(0,0,0,.45)" }}
                        //   />
                        // }
                        loading={loadingStaff}
                        showSearch
                        placeholder={
                          <React.Fragment>
                            {formatMessage(messages.choseType)}{" "}
                            {formatMessage(messages.followers).toLowerCase()}{" "}
                          </React.Fragment>
                        }
                        optionFilterProp="children"
                        filterOption={(input, option) =>
                          toLowerCaseNonAccentVietnamese(
                            option.props.children
                          ).indexOf(toLowerCaseNonAccentVietnamese(input)) >= 0
                        }
                      >
                        {staffList.map((staff) => {
                          return (
                            <Select.Option key={staff.id} value={staff.id}>
                              {staff.first_name || ""}
                            </Select.Option>
                          );
                        })}
                      </Select>
                    )}
                  </Form.Item>
                </Spin>

                {/* {!isEdit && ( */}
                <Form.Item label={formatMessage(messages.priority)}>
                  {getFieldDecorator("prioritize", {
                    initialValue: record ? record.prioritize : 0,
                  })(
                    <Select placeholder={""}>
                      <Select.Option value={1}>{`${formatMessage(
                        messages.yes
                      )}`}</Select.Option>
                      <Select.Option value={0}>{`${formatMessage(
                        messages.no
                      )}`}</Select.Option>
                    </Select>
                  )}
                </Form.Item>
                {/* )} */}
              </Col>
            </Form>
          </Col>
          <div
            style={{ width: "100%", display: "flex", justifyContent: "center" }}
          >
            <Button
              ghost
              type="danger"
              onClick={(e) => {
                Modal.confirm({
                  title: formatMessage(messages.cancelContent),
                  okText: formatMessage(messages.yes),
                  okType: "danger",
                  cancelText: formatMessage(messages.no),
                  onOk: () => this.props.history.push("/main/task/list"),
                  onCancel() {},
                });
              }}
            >
              {formatMessage(messages.cancel)}
            </Button>
            {isEdit && (
              <Button
                ghost
                type="primary"
                onClick={this.onSave}
                style={{ marginLeft: 10 }}
              >
                {formatMessage(messages.updateTask)}
              </Button>
            )}
            {!isEdit && (
              <>
                <Button
                  onClick={this.onSave}
                  style={{ marginLeft: 10 }}
                  ghost
                  type="primary"
                >
                  {formatMessage(messages.createTask)}
                </Button>
              </>
            )}
          </div>
        </Row>
      </div>
    );
  }
}

export default TaskForm;
